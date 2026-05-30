@extends('layouts.app')

@section('title', 'Buat Tiket Perbaikan')

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
    /* Fix Select2 height untuk konsistensi dengan Bootstrap */
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
    <a href="{{ route('tickets.index') }}" class="text-decoration-none text-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Tiket
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 text-dark">Formulir Laporan Kerusakan Mesin</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('tickets.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="machine_id" class="form-label">Mesin <span class="text-danger">*</span></label>
                    <select name="machine_id" id="machine_id" class="form-select @error('machine_id') is-invalid @enderror" required>
                        <option value="">-- Cari & Pilih Mesin --</option>
                        @foreach($machines as $machine)
                            <option value="{{ $machine->id }}" {{ old('machine_id') == $machine->id ? 'selected' : '' }}>
                                {{ $machine->fa_tag_no }} - {{ $machine->fa_desc }}
                                @if($machine->loc_code) ({{ $machine->loc_code }}) @endif
                            </option>
                        @endforeach
                    </select>
                    @error('machine_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="priority" class="form-label">Prioritas <span class="text-danger">*</span></label>
                    <select name="priority" id="priority" class="form-select @error('priority') is-invalid @enderror" required>
                        <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Rendah (Low)</option>
                        <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Sedang (Medium)</option>
                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>Tinggi (High)</option>
                    </select>
                    @error('priority')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="issue_description" class="form-label">Deskripsi Kerusakan <span class="text-danger">*</span></label>
                <textarea name="issue_description" id="issue_description" rows="4" class="form-control @error('issue_description') is-invalid @enderror" required placeholder="Jelaskan secara detail kerusakan yang terjadi...">{{ old('issue_description') }}</textarea>
                @error('issue_description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="photo" class="form-label">Foto / Lampiran Bukti (Opsional)</label>
                <input type="file" name="photo" id="photo" class="form-control @error('photo') is-invalid @enderror" accept="image/*">
                <div class="form-text">Format yang didukung: JPG, PNG, GIF. Ukuran maksimal: 2MB.</div>
                @error('photo')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-save me-1"></i> Simpan Tiket
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
        $('#machine_id').select2({
            theme: 'bootstrap-5',
            placeholder: '-- Cari & Pilih Mesin --',
            allowClear: true,
            width: '100%',
            language: {
                noResults: function() {
                    return "Mesin tidak ditemukan";
                },
                searching: function() {
                    return "Mencari...";
                }
            }
        });

        // Fix untuk error styling saat validasi
        $('#machine_id').on('select2:open', function() {
            $('.select2-container--bootstrap-5').addClass('select2-container--open');
        });
        $('#machine_id').on('select2:close', function() {
            $('.select2-container--bootstrap-5').removeClass('select2-container--open');
        });
    });
</script>
@endpush
