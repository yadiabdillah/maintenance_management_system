@extends('layouts.app')

@section('title', 'Dashboard - MMS')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary">Share</button>
            <button type="button" class="btn btn-sm btn-outline-secondary">Export</button>
        </div>
        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle">
            <i class="bi bi-calendar3"></i> This week
        </button>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary mb-3 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-uppercase mb-1">Total Job Hari Ini</h6>
                        <h2 class="mb-0">12</h2>
                    </div>
                    <i class="bi bi-clipboard-data fs-1"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-danger mb-3 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-uppercase mb-1">Tiket OPEN</h6>
                        <h2 class="mb-0">4</h2>
                    </div>
                    <i class="bi bi-exclamation-circle fs-1"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning mb-3 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-uppercase mb-1">IN PROGRESS</h6>
                        <h2 class="mb-0">3</h2>
                    </div>
                    <i class="bi bi-gear-wide-connected fs-1"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success mb-3 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-uppercase mb-1">SLA On-Time</h6>
                        <h2 class="mb-0">95%</h2>
                    </div>
                    <i class="bi bi-check2-circle fs-1"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart Section Placeholder -->
<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Aktivitas Maintenance (7 Hari Terakhir)</h5>
            </div>
            <div class="card-body text-center py-5">
                <!-- Bar Chart Placeholder -->
                <i class="bi bi-bar-chart text-muted" style="font-size: 5rem;"></i>
                <p class="text-muted mt-3">Grafik aktivitas akan ditampilkan di sini menggunakan Chart.js atau serupa.</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Tiket Terbaru</h5>
            </div>
            <div class="list-group list-group-flush">
                <a href="#" class="list-group-item list-group-item-action">
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1">Mesin Sewing Juki #012</h6>
                        <small class="text-danger">OPEN</small>
                    </div>
                    <p class="mb-1 small">Jarum patah, Lantai 2</p>
                    <small class="text-muted">3 menit yang lalu</small>
                </a>
                <a href="#" class="list-group-item list-group-item-action">
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1">Cutting Machine A</h6>
                        <small class="text-warning">IN PROGRESS</small>
                    </div>
                    <p class="mb-1 small">Mata pisau tumpul, Lantai 1</p>
                    <small class="text-muted">1 jam yang lalu</small>
                </a>
            </div>
            <div class="card-footer bg-white text-center">
                <a href="#" class="text-decoration-none small">Lihat semua tiket</a>
            </div>
        </div>
    </div>
</div>
@endsection
