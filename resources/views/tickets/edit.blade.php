@extends('layouts.app')

@section('title', 'Update Status Tiket')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
    .sparepart-row {
        background: #f8f9fa;
        padding: 10px 12px;
        border-radius: 6px;
        margin-bottom: 8px;
        border-left: 3px solid #0d6efd;
        transition: all 0.2s;
    }
    .sparepart-row:hover {
        background: #e9ecef;
    }
    .sparepart-row .btn-remove {
        color: #dc3545;
        cursor: pointer;
        font-size: 1.2rem;
        line-height: 1;
    }
    .sparepart-row .btn-remove:hover {
        color: #a71d2a;
    }
    .stock-badge {
        font-size: 0.75rem;
    }
    .select2-container--bootstrap-5 .select2-selection--single {
        height: calc(1.5em + 0.75rem + 2px) !important;
        padding: 0.375rem 0.75rem !important;
    }
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        padding: 0.375rem 0.75rem !important;
        line-height: 1.5 !important;
    }
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow {
        height: calc(1.5em + 0.75rem) !important;
    }
</style>
@endpush

@section('content')
<div class="mb-4">
    <a href="{{ route('tickets.show', $ticket->id) }}" class="text-decoration-none text-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Detail Tiket
    </a>
</div>

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 text-dark">Update Status & Prioritas Tiket</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('tickets.update', $ticket->id) }}" method="POST" id="ticketForm">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label text-muted mb-1">No. Tiket</label>
                        <div class="form-control bg-light">{{ $ticket->ticket_number }}</div>
                    </div>

                    <div class="mb-3">
                        <label for="priority" class="form-label">Prioritas</label>
                        <select name="priority" id="priority" class="form-select @error('priority') is-invalid @enderror" required>
                            <option value="low" {{ old('priority', $ticket->priority) == 'low' ? 'selected' : '' }}>Rendah (Low)</option>
                            <option value="medium" {{ old('priority', $ticket->priority) == 'medium' ? 'selected' : '' }}>Sedang (Medium)</option>
                            <option value="high" {{ old('priority', $ticket->priority) == 'high' ? 'selected' : '' }}>Tinggi (High)</option>
                        </select>
                        @error('priority')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @php
                        $isAdmin = in_array(Auth::user()->role, ['Super Admin', 'Supervisor']);
                        $isMechanic = Auth::user()->role === 'Operator';
                    @endphp

                    <div class="mb-4">
                        <label for="status" class="form-label">Status Tiket</label>
                        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                            @if($isAdmin)
                                <option value="open" {{ old('status', $ticket->status) == 'open' ? 'selected' : '' }}>Open</option>
                                <option value="in_progress" {{ old('status', $ticket->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            @endif
                            <option value="resolved" {{ old('status', $ticket->status) == 'resolved' ? 'selected' : '' }}>✅ Resolved (Selesai)</option>
                            <option value="closed" {{ old('status', $ticket->status) == 'closed' ? 'selected' : '' }}>🔒 Closed (Tutup)</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if($isMechanic)
                            <div class="form-text text-success">
                                <i class="bi bi-info-circle me-1"></i>
                                Anda hanya dapat menyelesaikan atau menutup tiket ini.
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <!-- Sparepart Usage Section -->
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="bi bi-box-seam me-1"></i> Sparepart Digunakan (Opsional)
                        </label>
                        <div id="sparepartContainer">
                            <!-- Dynamic sparepart rows will be added here -->
                        </div>
                        <button type="button" id="addSparepartBtn" class="btn btn-sm btn-outline-primary mt-2">
                            <i class="bi bi-plus-circle me-1"></i> Tambah Sparepart
                        </button>
                    </div>
                </div>
            </div>

            <hr>
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('tickets.show', $ticket->id) }}" class="btn btn-secondary px-4">
                    <i class="bi bi-x-circle me-1"></i> Batal
                </a>
                @if($isMechanic)
                    <button type="submit" class="btn btn-success px-4">
                        <i class="bi bi-check2-all me-1"></i> Selesaikan Tiket
                    </button>
                @else
                    <button type="submit" class="btn btn-warning px-4">
                        <i class="bi bi-save me-1"></i> Simpan Perubahan
                    </button>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    const searchUrl = '{{ route("spareparts.search") }}';
    let sparepartIndex = 0;

    function initSelect2(selectElement, existingId = null) {
        $(selectElement).select2({
            theme: 'bootstrap-5',
            ajax: {
                url: searchUrl,
                dataType: 'json',
                delay: 300,
                data: function(params) {
                    return {
                        term: params.term || '',
                        page: params.page || 1
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.results,
                        pagination: data.pagination
                    };
                },
                cache: true
            },
            placeholder: '-- Pilih Sparepart --',
            minimumInputLength: 0,
            templateResult: function(sparepart) {
                if (sparepart.loading) return sparepart.text;
                return $(`
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>${sparepart.text}</strong>
                        </div>
                        <span class="badge bg-info text-dark">Stok: ${sparepart.stock}</span>
                    </div>
                `);
            },
            templateSelection: function(sparepart) {
                if (!sparepart.id) return sparepart.text;
                return `${sparepart.text}`;
            }
        });

        // Update stock badge on selection change
        $(selectElement).on('select2:select', function(e) {
            const data = e.params.data;
            const row = $(this).closest('.sparepart-row');
            row.find('.stock-badge').text(`Stok: ${data.stock}`);
        });
    }

    function addSparepartRow(existingId = null, qty = 1) {
        const container = document.getElementById('sparepartContainer');
        const index = sparepartIndex++;

        const row = document.createElement('div');
        row.className = 'sparepart-row';
        row.id = `sparepart-row-${index}`;
        row.innerHTML = `
            <div class="d-flex align-items-center gap-2">
                <div style="flex: 1;">
                    <select name="spareparts[]" class="form-select form-select-sm sparepart-select" id="sparepart-select-${index}" required>
                        <option value="">-- Pilih Sparepart --</option>
                    </select>
                </div>
                <div style="width: 100px;">
                    <input type="number" name="qtys[]" class="form-control form-control-sm" value="${qty}" min="1" required placeholder="Qty">
                </div>
                <span class="stock-badge badge bg-info text-dark" id="stock-badge-${index}">Stok: -</span>
                <span class="btn-remove" onclick="removeSparepartRow(${index})">
                    <i class="bi bi-x-circle"></i>
                </span>
            </div>
        `;

        container.appendChild(row);

        // Initialize Select2 on the new select element
        const selectElement = document.getElementById(`sparepart-select-${index}`);
        initSelect2(selectElement, existingId);

        // If editing existing sparepart, set the value
        if (existingId) {
            // Fetch and set the existing value
            $.ajax({
                url: searchUrl,
                data: { term: '', page: 1 },
                dataType: 'json',
                success: function(data) {
                    const sparepart = data.results.find(s => s.id == existingId);
                    if (sparepart) {
                        const option = new Option(sparepart.text, sparepart.id, true, true);
                        $(selectElement).append(option).trigger('change');
                        $(selectElement).trigger({
                            type: 'select2:select',
                            params: { data: sparepart }
                        });
                    }
                }
            });
        }
    }

    function removeSparepartRow(index) {
        const row = document.getElementById(`sparepart-row-${index}`);
        if (row) {
            // Destroy Select2 before removing
            $(`#sparepart-select-${index}`).select2('destroy');
            row.remove();
        }
    }

    document.getElementById('addSparepartBtn').addEventListener('click', function() {
        addSparepartRow();
    });

    // Load existing spareparts if any
    @if(isset($ticket->spareparts) && $ticket->spareparts->count() > 0)
        @foreach($ticket->spareparts as $sp)
            addSparepartRow({{ $sp->id }}, {{ $sp->pivot->qty ?? 1 }});
        @endforeach
    @endif
</script>
@endpush