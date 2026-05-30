<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Stok Sparepart</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #333;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #198754;
        }
        .header h1 {
            font-size: 18px;
            color: #198754;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 11px;
            color: #666;
        }
        .summary {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            gap: 8px;
        }
        .summary-item {
            flex: 1;
            text-align: center;
            padding: 8px;
            border-radius: 4px;
            color: #fff;
            font-size: 11px;
        }
        .summary-item .number {
            font-size: 20px;
            font-weight: bold;
            display: block;
        }
        .bg-primary { background: #0d6efd; }
        .bg-info { background: #0dcaf0; color: #333; }
        .bg-danger { background: #dc3545; }
        .bg-warning { background: #ffc107; color: #333; }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }
        th {
            background: #198754;
            color: #fff;
            padding: 6px 4px;
            text-align: left;
            font-weight: 600;
        }
        th.center { text-align: center; }
        td {
            padding: 5px 4px;
            border-bottom: 1px solid #e0e0e0;
        }
        td.center { text-align: center; }
        tr:nth-child(even) { background: #f8f9fa; }
        .low-stock { background: #fff3f3 !important; }
        .badge {
            display: inline-block;
            padding: 1px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }
        .badge-danger { background: #dc3545; color: #fff; }
        .badge-success { background: #198754; color: #fff; }
        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 9px;
            color: #999;
            border-top: 1px solid #e0e0e0;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN STOK SPAREPART</h1>
        <p>
            Periode: {{ $startDate }} s/d {{ $endDate }}
            | Dicetak: {{ now()->format('d/m/Y H:i') }}
        </p>
    </div>

    <div class="summary">
        <div class="summary-item bg-primary">
            <span class="number">{{ $totalItems }}</span>
            Total Item
        </div>
        <div class="summary-item bg-info">
            <span class="number">{{ number_format($totalStock) }}</span>
            Total Stok
        </div>
        <div class="summary-item bg-danger">
            <span class="number">{{ $lowStock }}</span>
            Low Stock
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>SKU</th>
                <th>Nama Sparepart</th>
                <th class="center">Stok</th>
                <th class="center">Min</th>
                <th class="center">Status</th>
                <th class="center">Masuk</th>
                <th class="center">Keluar</th>
            </tr>
        </thead>
        <tbody>
            @forelse($spareparts as $sp)
            @php
                $isLowStock = $sp->stock <= $sp->min_stock;
                $txIn = \App\Models\SparepartTransaction::where('sparepart_id', $sp->id)
                    ->where('type', 'IN')
                    ->whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $endDate)
                    ->sum('qty');
                $txOut = \App\Models\SparepartTransaction::where('sparepart_id', $sp->id)
                    ->where('type', 'OUT')
                    ->whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $endDate)
                    ->sum('qty');
            @endphp
            <tr class="{{ $isLowStock ? 'low-stock' : '' }}">
                <td>{{ $sp->sku }}</td>
                <td>{{ $sp->name }}</td>
                <td class="center">{{ $sp->stock }}</td>
                <td class="center">{{ $sp->min_stock }}</td>
                <td class="center">
                    @if($isLowStock)
                        <span class="badge badge-danger">Low Stock</span>
                    @else
                        <span class="badge badge-success">Aman</span>
                    @endif
                </td>
                <td class="center">{{ $txIn }}</td>
                <td class="center">{{ $txOut }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 30px; color: #999;">
                    Tidak ada data sparepart.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>MMS - Maintenance Management System &bull; Dicetak {{ now()->format('d F Y H:i') }}</p>
    </div>
</body>
</html>
