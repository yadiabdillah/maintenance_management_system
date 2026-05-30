@extends('layouts.app')

@section('title', 'Assign Mekanik')

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
    .select2-container--bootstrap-5 .select2-selection--single {
        height: calc(3.5rem + 2px);
        padding-top: 0.75rem;
    }
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        line-height: 1.5;
    }
</style>
@endpush

@section('content')
<div class="mb-4">
    <a href="{{ route('tickets.show', $ticket->id) }}" class="text-decoration-none text-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Detail Tiket
    </a>
</div>

<div class="card border-0 shadow-sm max-w-lg">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 text-dark">Assign Mekanik ke Tiket: {{ $ticket->ticket_number }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('tickets.assign', $ticket->id) }}" method="POST">
            @csrf

            <!-- Informasi Tiket -->
            <div class="bg-light p-3 rounded mb-4">
                <div class="row">
                    <div class="col-md-6 mb-2 mb-md-0">
                        <small class="text-muted d-block">Mesin</small>
                        <strong>{{ $ticket->machine->fa_desc ?? 'N/A' }}</strong>
                        <span class="text-secondary small">({{ $ticket->machine->fa_tag_no ?? '-' }})</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Deskripsi Kerusakan</small>
                        <strong>{{ $ticket->issue_description }}</strong>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-6">
                        <small class="text-muted d-block">Prioritas</small>
                        @if($ticket->priority == 'low')
                            <span class="badge bg-secondary">Low</span>
                        @elseif($ticket->priority == 'medium')
                            <span class="badge bg-warning text-dark">Medium</span>
                        @else
                            <span class="badge bg-danger">High</span>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Status</small>
                        @if($ticket->status == 'open')
                            <span class="badge bg-primary">Open</span>
                        @elseif($ticket->status == 'in_progress')
                            <span class="badge bg-info text-dark">In Progress</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="assigned_to" class="form-label">Pilih Mekanik <span class="text-danger">*</span></label>
                <select name="assigned_to" id="assigned_to" class="form-select @error('assigned_to') is-invalid @enderror" required>
                    <option value="">-- Cari & Pilih Mekanik --</option>
                    @foreach($mechanics as $mechanic)
                        <option value="{{ $mechanic->id }}" {{ old('assigned_to', $ticket->assigned_to) == $mechanic->id ? 'selected' : '' }}>
                            {{ $mechanic->name }} - {{ $mechanic->email }}
                        </option>
                    @endforeach
                </select>
                @error('assigned_to')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                @if($mechanics->isEmpty())
                    <div class="text-warning small mt-1">
                        <i class="bi bi-exclamation-triangle me-1"></i> Tidak ada mekanik (Operator) aktif tersedia.
                        <a href="{{ route('users.create') }}" class="text-decoration-none">Buat pengguna baru</a>
                    </div>
                @endif
            </div>

            <div class="mb-4">
                <label for="sla_target_hours" class="form-label">Target SLA (Jam) <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="number" name="sla_target_hours" id="sla_target_hours"
                        class="form-control @error('sla_target_hours') is-invalid @enderror"
                        value="{{ old('sla_target_hours', $ticket->sla_target_hours ?? ($ticket->priority == 'low' ? 24 : ($ticket->priority == 'medium' ? 8 : 4))) }}"
                        min="1" max="168" required>
                    <span class="input-group-text">jam</span>
                    @error('sla_target_hours')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-text">
                    <i class="bi bi-info-circle me-1"></i>
                    Maksimal waktu yang diberikan untuk menyelesaikan pekerjaan ini.
                    Berdasarkan prioritas: Low=24jam, Medium=8jam, High=4jam.
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('tickets.show', $ticket->id) }}" class="btn btn-secondary px-4">
                    <i class="bi bi-x-circle me-1"></i> Batal
                </a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check2-circle me-1"></i> Assign Mekanik
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<!-- jQuery (required by Select2) -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#assigned_to').select2({
            theme: 'bootstrap-5',
            placeholder: '-- Cari & Pilih Mekanik --',
            allowClear: true,
            width: '100%',
            language: {
                noResults: function() {
                    return "Mekanik tidak ditemukan";
                },
                searching: function() {
                    return "Mencari...";
                }
            }
        });

        $('#assigned_to').on('select2:open', function() {
            $('.select2-container--bootstrap-5').addClass('select2-container--open');
        });
        $('#assigned_to').on('select2:close', function() {
            $('.select2-container--bootstrap-5').removeClass('select2-container--open');
        });
    });
</script>
@endpush
