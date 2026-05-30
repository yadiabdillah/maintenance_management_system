<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $isMechanic = $user->role === 'Operator';
        $isSupervisorOrAdmin = in_array($user->role, ['Super Admin', 'Supervisor']);

        // Base query - mekanik hanya lihat job-nya sendiri, supervisor/admin lihat semua
        if ($isMechanic) {
            $query = Ticket::where('assigned_to', $user->id);
        } else {
            $query = Ticket::query();
        }

        // Summary Cards
        $totalToday = (clone $query)->whereDate('created_at', today())->count();
        $totalOpen = (clone $query)->where('status', 'open')->count();
        $totalInProgress = (clone $query)->where('status', 'in_progress')->count();

        // SLA On-Time count (resolved tickets with SLA compliance)
        $resolvedTickets = (clone $query)->whereNotNull('started_at')
            ->whereNotNull('resolved_at')
            ->whereNotNull('sla_target_hours')
            ->get();

        $slaOnTimeCount = $resolvedTickets->filter(function ($ticket) {
            $durationHours = $ticket->started_at->diffInHours($ticket->resolved_at);
            $slaPct = ($durationHours / $ticket->sla_target_hours) * 100;
            return $slaPct <= 80;
        })->count();

        $totalResolved = $resolvedTickets->count();
        $slaPercentage = $totalResolved > 0 ? round(($slaOnTimeCount / $totalResolved) * 100) : 100;

        // Maintenance activity per day for last 7 days
        $ticketsLast7Days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dayLabel = now()->subDays($i)->format('D');

            $dayQuery = clone $query;
            $ticketsLast7Days->push([
                'date' => $date,
                'label' => $dayLabel,
                'open' => (clone $dayQuery)->whereDate('created_at', $date)->where('status', 'open')->count(),
                'in_progress' => (clone $dayQuery)->whereDate('created_at', $date)->where('status', 'in_progress')->count(),
                'resolved' => (clone $dayQuery)->whereDate('created_at', $date)->whereIn('status', ['resolved', 'closed'])->count(),
            ]);
        }

        // Latest tickets
        $latestTickets = (clone $query)->with(['machine', 'assignedMechanic'])
            ->latest()
            ->take(5)
            ->get();

        // My tickets (for mechanic)
        $myTickets = [];
        if ($isMechanic) {
            $myTickets = (clone $query)->with(['machine'])
                ->whereIn('status', ['open', 'in_progress'])
                ->latest()
                ->get();
        }

        // All open tickets needing assignment (for supervisor/admin)
        $unassignedTickets = [];
        if ($isSupervisorOrAdmin) {
            $unassignedTickets = Ticket::with(['machine'])
                ->where('status', 'open')
                ->whereNull('assigned_to')
                ->latest()
                ->take(10)
                ->get();
        }

        return view('dashboard', compact(
            'user',
            'totalToday',
            'totalOpen',
            'totalInProgress',
            'slaPercentage',
            'totalResolved',
            'slaOnTimeCount',
            'ticketsLast7Days',
            'latestTickets',
            'myTickets',
            'unassignedTickets',
            'isMechanic',
            'isSupervisorOrAdmin'
        ));
    }
}
