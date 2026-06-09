@extends('layouts.app')

@section('title', 'Ubah Foto Profil - MMS')

@push('styles')
<style>
    .avatar-preview {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #e9ecef;
        transition: all 0.3s ease;
    }
    .avatar-preview:hover {
        border-color: #0d6efd;
        transform: scale(1.02);
    }
    .upload-area {
        border: 2px dashed #dee2e6;
        border-radius: 12px;
        padding: 2rem;
        text-align: center;
        background: #f8f9fa;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .upload-area:hover {
        border-color: #0d6efd;
        background: #e7f1ff;
    }
    .upload-area.dragover {
        border-color: #0d6efd;
        background: #e7f1ff;
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('profile.show') }}">Profil Saya</a></li>
                <li class="breadcrumb-item active" aria-current="page">Ubah Foto</li>
            </ol>
        </nav>
        <h1 class="h2 fw-bold text-dark mb-0">
            <i class="bi bi-camera-fill me-2 text-primary"></i>Ubah Foto Profil
        </h1>
        <p class="text-muted mb-0">Unggah foto profil baru untuk akun Anda.</p>
    </div>
    <div>
        <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm border-0 rounded-3 bg-white p-4">
            <div class="card-body text-center">
                <!-- Current Avatar Preview -->
                <div class="mb-4">
                    <p class="text-muted small fw-semibold mb-3">Foto Saat Ini</p>
                    @if($user->avatar)
                        <img src="{{ asset('storage/avatars/' . $user->avatar) }}" alt="Avatar" 
                            class="avatar-preview" id="avatarPreview">
                    @else
                        @php
                            $avatarBg = '6c757d';
                            if ($user->role === 'Super Admin') {
                                $avatarBg = 'dc3545';
                            } elseif ($user->role === 'Supervisor') {
                                $avatarBg = 'ffc107';
                            }
                            $avatarColor = $user->role === 'Supervisor' ? '000' : 'fff';
                        @endphp
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background={{ $avatarBg }}&color={{ $avatarColor }}&bold=true&size=150" alt="Avatar" 
                            class="avatar-preview" id="avatarPreview">
                    @endif
                </div>

                <!-- Upload Form -->
                <form action="{{ route('profile.photo.update') }}" method="POST" enctype="multipart/form-data" id="photoForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="upload-area mb-4" id="uploadArea">
                        <i class="bi bi-cloud-arrow-up fs-1 text-primary mb-2"></i>
                        <p class="mb-1 fw-semibold text-dark">Klik atau seret foto ke sini</p>
                        <p class="mb-0 small text-muted">Format: JPG, PNG, GIF | Maks: 2MB</p>
                        <input type="file" name="avatar" id="avatarInput" accept="image/*" class="d-none" 
                            onchange="previewImage(this)">
                    </div>
                    @error('avatar')
                        <div class="alert alert-danger py-2 mb-3">
                            <i class="bi bi-exclamation-circle-fill me-2"></i>{{ $message }}
                        </div>
                    @enderror

                    <div class="d-flex gap-2 justify-content-center">
                        <button type="submit" class="btn btn-primary px-4 fw-semibold shadow-sm" id="uploadBtn" disabled>
                            <i class="bi bi-upload me-1"></i> Unggah Foto
                        </button>
                        <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary px-4">Batal</a>
                    </div>
                </form>

                <!-- Delete Photo -->
                @if($user->avatar)
                    <hr class="my-4">
                    <form action="{{ route('profile.photo.delete') }}" method="POST" 
                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus foto profil?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-trash me-1"></i> Hapus Foto
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const uploadArea = document.getElementById('uploadArea');
    const avatarInput = document.getElementById('avatarInput');
    const avatarPreview = document.getElementById('avatarPreview');
    const uploadBtn = document.getElementById('uploadBtn');

    // Click to upload
    uploadArea.addEventListener('click', () => avatarInput.click());

    // Drag and drop
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });

    uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('dragover');
    });

    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        if (e.dataTransfer.files.length) {
            avatarInput.files = e.dataTransfer.files;
            previewImage(avatarInput);
        }
    });

    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                avatarPreview.src = e.target.result;
                uploadBtn.disabled = false;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush