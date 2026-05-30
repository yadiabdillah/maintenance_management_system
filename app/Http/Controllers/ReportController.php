<?php

namespace App\Http\Controllers;

use App\Models\Sparepart;
use App\Models\SparepartTransaction;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * Tampilkan halaman filter laporan.
     */
    public function index(Request $request)
    {
        $mechanics = User::where('role', 'Operator')->where('is_active', true)->orderBy('name')->get();

        // Ambil data tiket berdasarkan filter untuk preview
        $query = Ticket::with(['machine', 'user', 'assignedMechanic']);

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Filter by technician (assigned mechanic)
        if ($request->filled('technician') && $request->technician !== 'all') {
            $query->where('assigned_to', $request->technician);
        }

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $tickets = $query->latest()->get();

        // Summary
        $totalTickets = $tickets->count();
        $totalOpen = $tickets->where('status', 'open')->count();
        $totalInProgress = $tickets->where('status', 'in_progress')->count();
        $totalResolved = $tickets->where('status', 'resolved')->count();
        $totalClosed = $tickets->where('status', 'closed')->count();

        return view('reports.index', compact(
            'mechanics', 'tickets',
            'totalTickets', 'totalOpen', 'totalInProgress', 'totalResolved', 'totalClosed'
        ));
    }

    /**
     * Export laporan ke CSV.
     */
    public function exportCsv(Request $request)
    {
        $tickets = $this->getFilteredTickets($request);

        $filename = 'laporan_tiket_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($tickets) {
            $handle = fopen('php://output', 'w');

            // BOM for UTF-8 Excel compatibility
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header row
            fputcsv($handle, [
                'No. Tiket',
                'Mesin',
                'Tag No',
                'Pelapor',
                'Mekanik',
                'Deskripsi',
                'Prioritas',
                'Status',
                'SLA Target (Jam)',
                'Mulai',
                'Selesai',
                'Dibuat',
            ]);

            // Data rows
            foreach ($tickets as $ticket) {
                fputcsv($handle, [
                    $ticket->ticket_number,
                    $ticket->machine?->fa_desc ?? 'N/A',
                    $ticket->machine?->fa_tag_no ?? '-',
                    $ticket->user?->name ?? 'System',
                    $ticket->assignedMechanic?->name ?? '-',
                    $ticket->issue_description,
                    ucfirst($ticket->priority),
                    ucfirst(str_replace('_', ' ', $ticket->status)),
                    $ticket->sla_target_hours ?? '-',
                    $ticket->started_at ? $ticket->started_at->format('d/m/Y H:i') : '-',
                    $ticket->resolved_at ? $ticket->resolved_at->format('d/m/Y H:i') : '-',
                    $ticket->created_at->format('d/m/Y H:i'),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export laporan ke PDF.
     */
    public function exportPdf(Request $request)
    {
        $tickets = $this->getFilteredTickets($request);

        $totalTickets = $tickets->count();
        $totalResolved = $tickets->whereIn('status', ['resolved', 'closed'])->count();
        $totalOpen = $tickets->where('status', 'open')->count();

        $pdf = Pdf::loadView('reports.pdf', compact(
            'tickets', 'totalTickets', 'totalResolved', 'totalOpen'
        ));

        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('laporan_tiket_' . now()->format('Ymd_His') . '.pdf');
    }

    /**
     * Get filtered tickets based on request parameters.
     */
    // ======================
    // SPAREPART STOCK REPORT
    // ======================

    /**
     * Laporan stok sparepart dengan filter tanggal/bulan.
     */
    public function sparepartStock(Request $request)
    {
        $query = Sparepart::query();

        // Filter pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $spareparts = $query->orderBy('name')->get();
        $totalItems = $spareparts->count();
        $totalStock = $spareparts->sum('stock');
        $lowStock = $spareparts->where('stock', '<=', 'min_stock')->count();
        $totalValue = $spareparts->sum(function ($s) {
            // Ambil rata-rata harga dari transaksi IN terakhir
            $lastPrice = SparepartTransaction::where('sparepart_id', $s->id)
                ->where('type', 'IN')
                ->whereNotNull('unit_price')
                ->latest()
                ->value('unit_price');
            return $s->stock * ($lastPrice ?? 0);
        });

        // Filter by date range for transaction summary
        $startDate = $request->filled('start_date') ? $request->start_date : now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->filled('end_date') ? $request->end_date : now()->format('Y-m-d');
        $filterType = $request->filter_type ?? 'daily'; // daily or monthly

        // Get transaction summary per sparepart
        $transactionSummary = [];
        foreach ($spareparts as $sp) {
            $txQuery = SparepartTransaction::where('sparepart_id', $sp->id)
                ->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate);

            $inQty = (clone $txQuery)->where('type', 'IN')->sum('qty');
            $outQty = (clone $txQuery)->where('type', 'OUT')->sum('qty');

            $transactionSummary[$sp->id] = [
                'in_qty' => $inQty,
                'out_qty' => $outQty,
            ];
        }

        // Monthly breakdown
        $monthlyData = [];
        if ($filterType === 'monthly') {
            $months = SparepartTransaction::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month")
                ->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('month');

            foreach ($months as $month) {
                $monthlyIn = SparepartTransaction::where('type', 'IN')
                    ->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$month])
                    ->sum('qty');
                $monthlyOut = SparepartTransaction::where('type', 'OUT')
                    ->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$month])
                    ->sum('qty');
                $monthlyData[] = [
                    'month' => $month,
                    'in_qty' => $monthlyIn,
                    'out_qty' => $monthlyOut,
                ];
            }
        }

        return view('reports.sparepart-stock', compact(
            'spareparts', 'totalItems', 'totalStock', 'lowStock', 'totalValue',
            'startDate', 'endDate', 'filterType',
            'transactionSummary', 'monthlyData'
        ));
    }

    /**
     * Export laporan stok sparepart ke CSV.
     */
    public function sparepartStockCsv(Request $request)
    {
        $spareparts = Sparepart::orderBy('name')->get();
        $startDate = $request->filled('start_date') ? $request->start_date : now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->filled('end_date') ? $request->end_date : now()->format('Y-m-d');

        $filename = 'laporan_stok_sparepart_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($spareparts, $startDate, $endDate) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, [
                'SKU',
                'Nama Sparepart',
                'Stok Saat Ini',
                'Min Stok',
                'Status Stok',
                "Masuk ($startDate - $endDate)",
                "Keluar ($startDate - $endDate)",
            ]);

            foreach ($spareparts as $sp) {
                $txIn = SparepartTransaction::where('sparepart_id', $sp->id)
                    ->where('type', 'IN')
                    ->whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $endDate)
                    ->sum('qty');
                $txOut = SparepartTransaction::where('sparepart_id', $sp->id)
                    ->where('type', 'OUT')
                    ->whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $endDate)
                    ->sum('qty');

                $status = $sp->stock <= $sp->min_stock ? 'Low Stock' : 'Aman';

                fputcsv($handle, [
                    $sp->sku,
                    $sp->name,
                    $sp->stock,
                    $sp->min_stock,
                    $status,
                    $txIn,
                    $txOut,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export laporan stok sparepart ke PDF.
     */
    public function sparepartStockPdf(Request $request)
    {
        $spareparts = Sparepart::orderBy('name')->get();
        $startDate = $request->filled('start_date') ? $request->start_date : now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->filled('end_date') ? $request->end_date : now()->format('Y-m-d');

        $totalItems = $spareparts->count();
        $totalStock = $spareparts->sum('stock');
        $lowStock = $spareparts->where('stock', '<=', 'min_stock')->count();

        $pdf = Pdf::loadView('reports.sparepart-stock-pdf', compact(
            'spareparts', 'totalItems', 'totalStock', 'lowStock',
            'startDate', 'endDate'
        ));

        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('laporan_stok_sparepart_' . now()->format('Ymd_His') . '.pdf');
    }

    private function getFilteredTickets(Request $request)
    {
        $query = Ticket::with(['machine', 'user', 'assignedMechanic', 'spareparts']);

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Filter by technician (assigned mechanic)
        if ($request->filled('technician') && $request->technician !== 'all') {
            $query->where('assigned_to', $request->technician);
        }

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        return $query->latest()->get();
    }
}
