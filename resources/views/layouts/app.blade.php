<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Maintenance Management System')</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
            overflow-x: hidden;
        }
        .sidebar {
            width: 250px;
            min-height: 100vh;
            background-color: #343a40;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1040;
            transition: transform 0.3s ease-in-out;
        }
        .sidebar a.nav-link {
            color: #adb5bd;
            padding: 10px 15px;
            margin-bottom: 5px;
            border-radius: 5px;
        }
        .sidebar a.nav-link:hover, .sidebar a.nav-link.active {
            color: #fff;
            background-color: #495057;
        }
        .main-content {
            margin-left: 250px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left 0.3s ease-in-out;
        }
        .top-navbar {
            background-color: #fff;
            border-bottom: 1px solid #dee2e6;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Mobile Responsive Styles */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%); /* Hide sidebar off-screen by default */
            }
            .sidebar.show-sidebar {
                transform: translateX(0); /* Show sidebar */
            }
            .main-content {
                margin-left: 0; /* Full width content */
            }
            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100vw;
                height: 100vh;
                background: rgba(0,0,0,0.5);
                z-index: 1030;
            }
            .sidebar-overlay.show {
                display: block;
            }
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar Menu -->
    <div class="sidebar d-flex flex-column p-3 text-white" id="sidebar">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="/" class="d-flex align-items-center text-white text-decoration-none">
                <i class="bi bi-tools fs-3 me-2 text-warning"></i>
                <span class="fs-4 fw-bold">MMS Admin</span>
            </a>
            <button class="btn text-white d-md-none p-0" id="closeSidebarBtn">
                <i class="bi bi-x-lg fs-4"></i>
            </button>
        </div>

        <div class="text-uppercase text-secondary small fw-bold mb-2 mt-2 px-2">Menu Utama</div>
        <ul class="nav flex-column mb-auto">
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('tickets.index') }}" class="nav-link {{ request()->routeIs('tickets.*') ? 'active' : '' }}">
                    <i class="bi bi-ticket-detailed me-2"></i> Tiket Perbaikan
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.index') || request()->routeIs('reports.export.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-bar-graph me-2"></i> Laporan Tiket
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('reports.machine.data') }}" class="nav-link {{ request()->routeIs('reports.machine.*') ? 'active' : '' }}">
                    <i class="bi bi-cpu me-2"></i> Laporan Data Mesin
                </a>
            </li>
        </ul>

        <div class="text-uppercase text-secondary small fw-bold mb-2 mt-4 px-2">Master Data</div>
        <ul class="nav flex-column mb-auto">
            <li class="nav-item">
                <a href="{{ route('machines.index') }}" class="nav-link {{ request()->routeIs('machines.*') ? 'active' : '' }}">
                    <i class="bi bi-cpu me-2"></i> Data Mesin
                </a>
            </li>
        </ul>

        <div class="text-uppercase text-secondary small fw-bold mb-2 mt-4 px-2">Inventaris</div>
        <ul class="nav flex-column mb-auto">
            <li class="nav-item">
                <a href="{{ route('spareparts.index') }}" class="nav-link {{ request()->routeIs('spareparts.*') ? 'active' : '' }}">
                    <i class="bi bi-box-seam me-2"></i> Stok Sparepart
                </a>
            </li>
             <li class="nav-item">
                <a href="{{ route('reports.sparepart.stock') }}" class="nav-link {{ request()->routeIs('reports.sparepart.*') ? 'active' : '' }}">
                    <i class="bi bi-box-seam me-2"></i> Laporan Stok
                </a>
            </li>
        </ul>

        <div class="text-uppercase text-secondary small fw-bold mb-2 mt-4 px-2">Sistem</div>
        <ul class="nav flex-column mb-auto">
            <li class="nav-item">
                <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <i class="bi bi-people me-2"></i> Manajemen Pengguna
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-gear me-2"></i> Pengaturan
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content Area -->
    <div class="main-content">
        <!-- Top Navbar (Profile & Logout) -->
        <div class="top-navbar shadow-sm sticky-top">
            <div class="d-flex align-items-center">
                <!-- Hamburger Menu Button -->
                <button class="btn btn-light d-md-none me-3 border shadow-sm" id="toggleSidebarBtn">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <h5 class="mb-0 text-dark d-none d-md-block">@yield('title', 'Dashboard')</h5>
                <h5 class="mb-0 text-dark d-md-none fw-bold">MMS</h5>
            </div>

            <div class="d-flex align-items-center">
                <!-- Notifikasi -->
                <a href="#" class="text-secondary me-4 position-relative">
                    <i class="bi bi-bell fs-5"></i>
                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                        <span class="visually-hidden">New alerts</span>
                    </span>
                </a>

                <!-- Profile Dropdown -->
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                        @if(Auth::user()->avatar)
                            <img src="{{ asset('storage/avatars/' . Auth::user()->avatar) }}" alt="" width="35" height="35" class="rounded-circle me-2 border" style="object-fit: cover;">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=0D8ABC&color=fff" alt="" width="35" height="35" class="rounded-circle me-2 border">
                        @endif
                        <div class="d-none d-md-block text-start">
                            <strong class="d-block lh-1">{{ Auth::user()->name }}</strong>
                            <small class="text-muted" style="font-size: 0.75rem;">{{ Auth::user()->role }}</small>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow mt-2" aria-labelledby="dropdownUser">
                        <li><a class="dropdown-item" href="{{ route('profile.show') }}"><i class="bi bi-person me-2"></i> Profil Saya</a></li>
                        <li><a class="dropdown-item" href="{{ route('profile.password') }}"><i class="bi bi-shield-lock me-2"></i> Ganti Password</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <div class="p-4 flex-grow-1">
            @yield('content')
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Logika untuk menampilkan dan menyembunyikan sidebar di layar mobile
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('toggleSidebarBtn');
            const closeBtn = document.getElementById('closeSidebarBtn');
            const overlay = document.getElementById('sidebarOverlay');

            function toggleSidebar() {
                sidebar.classList.toggle('show-sidebar');
                overlay.classList.toggle('show');
            }

            if(toggleBtn) toggleBtn.addEventListener('click', toggleSidebar);
            if(closeBtn) closeBtn.addEventListener('click', toggleSidebar);
            if(overlay) overlay.addEventListener('click', toggleSidebar);
        });
    </script>
    @stack('scripts')
</body>
</html>
