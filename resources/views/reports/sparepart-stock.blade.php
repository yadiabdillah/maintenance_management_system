@extends('layouts.app')

@section('title', 'Laporan Stok Sparepart - MMS')

@push('styles')
<style>
    .filter-card {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }
    .filter-card label {
        color: rgba(255,255,255,0.85);
        font-size: 0.85rem;
        font-weight: 500;
    }
    .filter-card .form-control,
    .filter-card .form-select {
        border: 1px solid rgba(255,255,255,0.3);
        background: rgba(255,255,255,0.15);
        color: #fff;
    }
    .filter-card .form-control::placeholder { color: rgba(255,255,255,0.5); }
    .filter-card .form-control:focus,
    .filter-card .form-select:focus {
        background: rgba(255,255,255,0.25);
        border-color: rgba(255,255,255,0.6);
        color: #fff;
        box-shadow: 0 0 0 0.2rem rgba(255,255,255,0.15);
    }
    .filter-card .form-select option { background: #11998e; color: #fff; }
    .stat-card { transition: transform 0.15s; }
    .stat-card:hover { transform: translateY(-2px); }
    .low-stock { background-color: #fff3f3; }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-box-seam me-2 text-success"></i> Laporan Stok Sparepart
    </h1>
</div>

<!-- Filter Card -->
<div class="card filter-card shadow-sm mb-4 border-0">
    <div class="card-body">
        <form action="{{ route('reports.sparepart.stock') }}" method="GET" id="filterForm">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="start_date" class="form-label"><i class="bi bi-calendar me-1"></i>Tanggal Mulai</label>
                    <input type="date" name="start_date" id="start_date" class="form-control"
                        value="{{ request('start_date', $startDate) }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label"><i class="bi bi-calendar me-1"></i>Tanggal Akhir</label>
                    <input type="date" name="end_date" id="end_date" class="form-control"
                        value="{{ request('end_date', $endDate) }}">
                </div>
                <div class="col-md-2">
                    <label for="filter_type" class="form-label"><i class="bi bi-layout-three-columns me-1"></i>Tipe Filter</label>
                    <select name="filter_type" id="filter_type" class="form-select">
                        <option value="daily" {{ request('filter_type', 'daily') == 'daily' ? 'selected' : '' }}>Harian</option>
                        <option value="monthly" {{ request('filter_type') == 'monthly' ? 'selected' : '' }}>Bulanan</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="search" class="form-label"><i class="bi bi-search me-1"></i>Cari</label>
                    <input type="text" name="search" id="search" class="form-control"
                        placeholder="Nama/SKU..." value="{{ request('search') }}">
                </div>
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-light w-100">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <a href="{{ route('reports.sparepart.stock') }}" class="btn btn-outline-light w-100">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card stat-card border-0 shadow-sm bg-primary text-white">
            <div class="card-body text-center py-3">
                <h3 class="mb-0">{{ $totalItems }}</h3>
                <small>Total Item</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card border-0 shadow-sm bg-info text-white">
            <div class="card-body text-center py-3">
                <h3 class="mb-0">{{ number_format($totalStock) }}</h3>
                <small>Total Stok</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card border-0 shadow-sm {{ $lowStock > 0 ? 'bg-danger' : 'bg-success' }} text-white">
            <div class="card-body text-center py-3">
                <h3 class="mb-0">{{ $lowStock }}</h3>
                <small>Low Stock Alert</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card border-0 shadow-sm bg-warning text-dark">
            <div class="card-body text-center py-3">
                <h3 class="mb-0">Rp {{ number_format($totalValue, 0, ',', '.') }}</h3>
                <small>Total Nilai Stok</small>
            </div>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <span class="text-muted">
        @if(request('start_date') || request('end_date'))
            Periode: <strong>{{ $startDate }}</strong> s/d <strong>{{ $endDate }}</strong>
            ({{ $filterType == 'monthly' ? 'Bulanan' : 'Harian' }})
        @else
            <i class="bi bi-info-circle me-1"></i> Menampilkan data stok terkini & transaksi dalam rentang filter
        @endif
    </span>
    <div class="d-flex gap-2">
        <a href="{{ route('reports.sparepart.stock.csv', request()->query()) }}" class="btn btn-success">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i> Download CSV
        </a>
        <a href="{{ route('reports.sparepart.stock.pdf', request()->query()) }}" class="btn btn-danger">
            <i class="bi bi-file-earmark-pdf me-1"></i> Download PDF
        </a>
    </div>
</div>

<!-- Main Table: Stok Sparepart -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0"><i class="bi bi-list-check me-2 text-success"></i>Daftar Stok Sparepart</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">SKU</th>
                        <th>Nama Sparepart</th>
                        <th class="text-center">Stok</th>
                        <th class="text-center">Min Stok</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Masuk</th>
                        <th class="text-center">Keluar</th>
                        <th class="text-center">Nilai Stok</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($spareparts as $sp)
                    @php
                        $isLowStock = $sp->stock <= $sp->min_stock;
                        $tx = $transactionSummary[$sp->id] ?? ['in_qty' => 0, 'out_qty' => 0];
                        $lastPrice = \App\Models\SparepartTransaction::where('sparepart_id', $sp->id)
                            ->where('type', 'IN')->whereNotNull('unit_price')->latest()->value('unit_price');
                        $value = $sp->stock * ($lastPrice ?? 0);
                    @endphp
                    <tr class="{{ $isLowStock ? 'low-stock' : '' }}">
                        <td class="ps-3"><code>{{ $sp->sku }}</code></td>
                        <td>
                            <span class="fw-medium">{{ $sp->name }}</span>
                        </td>
                        <td class="text-center fw-bold {{ $isLowStock ? 'text-danger' : 'text-success' }}">
                            {{ $sp->stock }}
                        </td>
                        <td class="text-center">{{ $sp->min_stock }}</td>
                        <td class="text-center">
                            @if($isLowStock)
                                <span class="badge bg-danger"><i class="bi bi-exclamation-triangle me-1"></i>Low Stock</span>
                            @else
                                <span class="badge bg-success">Aman</span>
                            @endif
                        </td>
                        <td class="text-center text-primary fw-bold">{{ $tx['in_qty'] }}</td>
                        <td class="text-center text-danger fw-bold">{{ $tx['out_qty'] }}</td>
                        <td class="text-center">Rp {{ number_format($value, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                            <p class="mt-2 mb-0">Tidak ada data sparepart</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($spareparts->hasPages())
        <div class="card-footer bg-white border-top py-3">
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-muted small">Menampilkan {{ $spareparts->firstItem() }} - {{ $spareparts->lastItem() }} dari {{ $spareparts->total() }} sparepart</span>
                <div>{{ $spareparts->links() }}</div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Monthly Breakdown -->
@if($filterType == 'monthly' && count($monthlyData) > 0)
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0"><i class="bi bi-bar-chart-line me-2 text-info"></i>Rekap Bulanan Transaksi Sparepart</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Bulan</th>
                        <th class="text-center text-primary">Total Masuk</th>
                        <th class="text-center text-danger">Total Keluar</th>
                        <th class="text-center">Selisih</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($monthlyData as $md)
                    @php $diff = $md['in_qty'] - $md['out_qty']; @endphp
                    <tr>
                        <td class="ps-3 fw-medium">{{ \Carbon\Carbon::createFromFormat('Y-m', $md['month'])->format('F Y') }}</td>
                        <td class="text-center text-primary fw-bold">{{ $md['in_qty'] }}</td>
                        <td class="text-center text-danger fw-bold">{{ $md['out_qty'] }}</td>
                        <td class="text-center fw-bold {{ $diff >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $diff >= 0 ? '+' : '' }}{{ $diff }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
@endsection
