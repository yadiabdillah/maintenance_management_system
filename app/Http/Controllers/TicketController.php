<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Machine;
use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Ticket::with(['machine', 'user', 'assignedMechanic']);

        // Mekanik hanya lihat tiket yang di-assign ke dirinya sendiri
        if ($user->role === 'Operator') {
            $query->where('assigned_to', $user->id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Get status from query string for active filter display
        $activeFilter = $request->input('status', '');

        $tickets = $query->latest()->paginate(10)->withQueryString();

        return view('tickets.index', compact('tickets', 'activeFilter'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $machines = Machine::all();
        return view('tickets.create', compact('machines'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'machine_id' => 'required|exists:machines,id',
            'issue_description' => 'required|string',
            'priority' => 'required|in:low,medium,high',
            'photo' => 'nullable|image|max:2048',
        ]);

        $data = $request->except('photo');
        $data['user_id'] = Auth::id();
        $data['ticket_number'] = 'TIK-' . strtoupper(Str::random(6)) . '-' . date('Ymd');
        $data['status'] = 'open';

        // Set SLA target hours based on priority
        $data['sla_target_hours'] = match ($request->priority) {
            'low' => 24,
            'medium' => 8,
            'high' => 4,
            default => 8,
        };

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('tickets', 'public');
        }

        Ticket::create($data);

        return redirect()->route('tickets.index')->with('success', 'Tiket perbaikan berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket)
    {
        $ticket->load(['machine', 'user', 'assignedMechanic', 'spareparts']);

        // Mekanik hanya bisa lihat tiket miliknya sendiri
        if (Auth::user()->role === 'Operator' && $ticket->assigned_to !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke tiket ini.');
        }

        return view('tickets.show', compact('ticket'));
    }

    /**
     * Show the form for editing the specified resource.
     * Admin/Supervisor bisa edit semua, Mekanik bisa edit tiketnya sendiri (untuk finish)
     */
    public function edit(Ticket $ticket)
    {
        $user = Auth::user();

        // Load spareparts with pivot data
        $ticket->load('spareparts');

        // Admin/Supervisor always allowed
        if (in_array($user->role, ['Super Admin', 'Supervisor'])) {
            $spareparts = Sparepart::orderBy('name')->get();
            return view('tickets.edit', compact('ticket', 'spareparts'));
        }

        // Mekanik hanya bisa edit tiket miliknya sendiri yang sedang dikerjakan
        if ($user->role === 'Operator' && $ticket->assigned_to === $user->id && $ticket->status === 'in_progress') {
            $spareparts = Sparepart::orderBy('name')->get();
            return view('tickets.edit', compact('ticket', 'spareparts'));
        }

        abort(403, 'Unauthorized action.');
    }

    /**
     * Update the specified resource in storage.
     * Admin/Supervisor bisa update apa saja.
     * Mekanik hanya bisa finish (resolved/closed) tiket miliknya sendiri.
     */
    public function update(Request $request, Ticket $ticket)
    {
        $user = Auth::user();
        $isAdmin = in_array($user->role, ['Super Admin', 'Supervisor']);
        $isAssignedMechanic = ($user->role === 'Operator' && $ticket->assigned_to === $user->id);

        // Authorization
        if (!$isAdmin && !$isAssignedMechanic) {
            abort(403, 'Unauthorized action.');
        }

        $validStatuses = ['open', 'in_progress', 'resolved', 'closed'];

        // Mekanik hanya boleh menyelesaikan tiket (resolved/closed), tidak bisa ubah status lain
        if ($isAssignedMechanic) {
            $validStatuses = ['resolved', 'closed'];
        }

        $request->validate([
            'status' => 'required|in:' . implode(',', $validStatuses),
            'priority' => 'required|in:low,medium,high',
            'spareparts' => 'nullable|array',
            'spareparts.*' => 'exists:spareparts,id',
            'qtys' => 'nullable|array',
            'qtys.*' => 'integer|min:1',
        ]);

        $updateData = [
            'status' => $request->status,
            'priority' => $request->priority,
        ];

        // Update SLA target hours when priority changes
        $updateData['sla_target_hours'] = match ($request->priority) {
            'low' => 24,
            'medium' => 8,
            'high' => 4,
            default => 8,
        };

        // Track start time when moving to in_progress
        if ($request->status === 'in_progress' && $ticket->status !== 'in_progress') {
            $updateData['started_at'] = now();
        }

        // Track resolve time and calculate SLA compliance
        if (in_array($request->status, ['resolved', 'closed']) && !$ticket->resolved_at) {
            $updateData['resolved_at'] = now();
        }

        // Use DB transaction for sparepart stock management
        try {
            DB::transaction(function () use ($request, $ticket, $updateData) {
                $ticket->update($updateData);

                // Process spareparts usage
                if ($request->filled('spareparts')) {
                    $syncData = [];

                    foreach ($request->spareparts as $index => $sparepartId) {
                        $qty = $request->qtys[$index] ?? 1;

                        // Deduct stock
                        $sparepart = Sparepart::findOrFail($sparepartId);
                        if ($sparepart->stock < $qty) {
                            throw new \Exception("Stok {$sparepart->name} tidak mencukupi. Stok tersedia: {$sparepart->stock}, dibutuhkan: {$qty}");
                        }

                        $sparepart->decrement('stock', $qty);
                        $syncData[$sparepartId] = ['qty' => $qty];
                    }

                    $ticket->spareparts()->sync($syncData);
                }
            });
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('tickets.show', $ticket->id)->with('success', 'Status tiket berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {
        // Authorization check
        if (!in_array(Auth::user()->role, ['Super Admin', 'Supervisor'])) {
            abort(403, 'Unauthorized action.');
        }

        $ticket->delete();
        return redirect()->route('tickets.index')->with('success', 'Tiket berhasil dihapus.');
    }

    /**
     * Show the form to assign a mechanic to a ticket.
     */
    public function assignForm(Ticket $ticket)
    {
        // Authorization: only Super Admin and Supervisor can assign
        if (!in_array(Auth::user()->role, ['Super Admin', 'Supervisor'])) {
            abort(403, 'Unauthorized action.');
        }

        // Only allow assigning if ticket is still open
        if (!in_array($ticket->status, ['open', 'in_progress'])) {
            return redirect()->route('tickets.show', $ticket->id)
                ->with('error', 'Tiket sudah selesai atau ditutup, tidak bisa di-assign ulang.');
        }

        // Get all active users with role Operator (mekanik)
        $mechanics = User::where('role', 'Operator')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('tickets.assign', compact('ticket', 'mechanics'));
    }

    /**
     * Assign a mechanic to a ticket and set SLA.
     */
    public function assign(Request $request, Ticket $ticket)
    {
        // Authorization: only Super Admin and Supervisor can assign
        if (!in_array(Auth::user()->role, ['Super Admin', 'Supervisor'])) {
            abort(403, 'Unauthorized action.');
        }

        // Only allow assigning if ticket is still open
        if (!in_array($ticket->status, ['open', 'in_progress'])) {
            return redirect()->route('tickets.show', $ticket->id)
                ->with('error', 'Tiket sudah selesai atau ditutup, tidak bisa di-assign ulang.');
        }

        $request->validate([
            'assigned_to' => 'required|exists:users,id',
            'sla_target_hours' => 'required|numeric|min:1|max:168', // max 7 days
        ]);

        // Verify the assigned user is an Operator (mekanik)
        $mechanic = User::findOrFail($request->assigned_to);
        if ($mechanic->role !== 'Operator') {
            return back()->with('error', 'Hanya pengguna dengan role Operator yang dapat ditugaskan sebagai mekanik.');
        }

        $updateData = [
            'assigned_to' => $request->assigned_to,
            'sla_target_hours' => $request->sla_target_hours,
        ];

        // Auto set status to in_progress and record start time
        if ($ticket->status === 'open') {
            $updateData['status'] = 'in_progress';
            $updateData['started_at'] = now();
        }

        $ticket->update($updateData);

        $mechanicName = $mechanic->name;
        return redirect()->route('tickets.show', $ticket->id)
            ->with('success', "Mekanik {$mechanicName} berhasil ditugaskan ke tiket {$ticket->ticket_number}.");
    }
}
