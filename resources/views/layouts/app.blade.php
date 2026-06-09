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
    <!-- Google Fonts - Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --sidebar-bg: #343a40;
            --sidebar-hover-bg: #495057;
            --sidebar-active-bg: #0d6efd;
            --sidebar-brand-bg: #212529;
            --sidebar-width: 250px;
            --topnav-height: 57px;
        }

        * {
            font-family: 'Source Sans 3', 'Source Sans Pro', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        body {
            background-color: #f4f6f9;
            overflow-x: hidden;
        }

        /* ===================== SIDEBAR ===================== */
        .sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: linear-gradient(180deg, var(--sidebar-brand-bg) 0%, var(--sidebar-bg) 100%);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1040;
            transition: transform 0.3s ease-in-out;
            display: flex;
            flex-direction: column;
        }

        /* Brand */
        .sidebar-brand {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            background-color: var(--sidebar-brand-bg);
            border-bottom: 1px solid rgba(255,255,255,0.08);
            min-height: 57px;
        }
        .sidebar-brand .brand-icon {
            width: 33px;
            height: 33px;
            background: linear-gradient(135deg, #ffc107, #ff9800);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            color: #fff;
            flex-shrink: 0;
        }
        .sidebar-brand .brand-text {
            margin-left: 10px;
            font-size: 1.15rem;
            font-weight: 700;
            color: #fff;
            letter-spacing: 0.3px;
        }

        /* User Panel */
        .sidebar-user {
            padding: 14px 16px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            display: flex;
            align-items: center;
        }
        .sidebar-user img {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            border: 2px solid rgba(255,255,255,0.2);
            object-fit: cover;
            flex-shrink: 0;
        }
        .sidebar-user .user-info {
            margin-left: 10px;
            overflow: hidden;
        }
        .sidebar-user .user-info .user-name {
            font-size: 0.875rem;
            font-weight: 600;
            color: #fff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .sidebar-user .user-info .user-role {
            font-size: 0.75rem;
            color: rgba(255,255,255,0.55);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Navigation */
        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: 8px 0;
        }
        .sidebar-nav::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar-nav::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.15);
            border-radius: 2px;
        }

        /* Section Header */
        .nav-section {
            padding: 16px 16px 6px 16px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: rgba(255,255,255,0.35);
        }

        /* Nav Items */
        .nav-sidebar .nav-item {
            margin: 1px 8px;
        }
        .nav-sidebar .nav-link {
            display: flex;
            align-items: center;
            padding: 10px 12px;
            color: rgba(255,255,255,0.65);
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 400;
            transition: all 0.15s ease-in-out;
            text-decoration: none;
            position: relative;
        }
        .nav-sidebar .nav-link i {
            width: 20px;
            text-align: center;
            margin-right: 10px;
            font-size: 1rem;
            opacity: 0.75;
        }
        .nav-sidebar .nav-link:hover {
            color: #fff;
            background-color: var(--sidebar-hover-bg);
        }
        .nav-sidebar .nav-link:hover i {
            opacity: 1;
        }
        .nav-sidebar .nav-link.active {
            color: #fff;
            background-color: var(--sidebar-active-bg);
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(13,110,253,0.3);
        }
        .nav-sidebar .nav-link.active i {
            opacity: 1;
        }
        .nav-sidebar .nav-link .badge {
            margin-left: auto;
            font-size: 0.65rem;
            padding: 3px 7px;
        }

        /* ===================== MAIN CONTENT ===================== */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left 0.3s ease-in-out;
        }

        /* Top Navbar */
        .top-navbar {
            background-color: #fff;
            border-bottom: 1px solid #dee2e6;
            padding: 0 20px;
            height: var(--topnav-height);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1030;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .top-navbar .left-section {
            display: flex;
            align-items: center;
        }
        .top-navbar .left-section h5 {
            margin: 0;
            font-size: 1.05rem;
            font-weight: 600;
            color: #495057;
        }
        .top-navbar .right-section {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* Profile Dropdown in Navbar */
        .navbar-profile {
            display: flex;
            align-items: center;
            padding: 4px 8px;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.15s;
            text-decoration: none;
            color: #343a40;
        }
        .navbar-profile:hover {
            background-color: #f1f3f5;
            color: #343a40;
        }
        .navbar-profile img {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #dee2e6;
        }
        .navbar-profile .profile-text {
            margin-left: 8px;
        }
        .navbar-profile .profile-text .name {
            font-size: 0.85rem;
            font-weight: 600;
            line-height: 1.1;
        }
        .navbar-profile .profile-text .role {
            font-size: 0.7rem;
            color: #6c757d;
        }

        /* Notification Badge */
        .notification-btn {
            position: relative;
            color: #6c757d;
            padding: 6px 8px;
            border-radius: 6px;
            transition: all 0.15s;
        }
        .notification-btn:hover {
            background-color: #f1f3f5;
            color: #343a40;
        }
        .notification-btn .badge-dot {
            position: absolute;
            top: 4px;
            right: 4px;
            width: 8px;
            height: 8px;
            background-color: #dc3545;
            border-radius: 50%;
            border: 2px solid #fff;
        }

        /* Sidebar Overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0,0,0,0.5);
            z-index: 1035;
            backdrop-filter: blur(2px);
        }
        .sidebar-overlay.show {
            display: block;
        }

        /* ===================== MOBILE ===================== */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show-sidebar {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
            .navbar-profile .profile-text {
                display: none;
            }
        }

        /* ===================== SCROLLBAR ===================== */
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #a1a1a1;
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <!-- Brand -->
        <a href="{{ route('dashboard') }}" class="sidebar-brand text-decoration-none">
            <div class="brand-icon">
                <i class="bi bi-tools"></i>
            </div>
            <span class="brand-text">MMS</span>
            <button class="btn text-white d-md-none p-0 ms-auto" id="closeSidebarBtn" style="background:none;border:none;">
                <i class="bi bi-x-lg"></i>
            </button>
        </a>

        <!-- User Panel -->
        <div class="sidebar-user">
            @if(Auth::user()->avatar)
                <img src="{{ asset('storage/avatars/' . Auth::user()->avatar) }}" alt="Avatar">
            @else
                @php
                    $avatarBg = '0D8ABC';
                    if (Auth::user()->role === 'Super Admin') $avatarBg = 'dc3545';
                    elseif (Auth::user()->role === 'Supervisor') $avatarBg = 'ffc107';
                @endphp
                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background={{ $avatarBg }}&color=fff&bold=true&size=70" alt="Avatar">
            @endif
            <div class="user-info">
                <div class="user-name">{{ Auth::user()->name }}</div>
                <div class="user-role">{{ Auth::user()->role }}</div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="sidebar-nav">
            <ul class="nav flex-column nav-sidebar">
                <!-- Menu Utama -->
                <li class="nav-section">Menu Utama</li>
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-grid-1x2-fill"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('tickets.index') }}" class="nav-link {{ request()->routeIs('tickets.*') ? 'active' : '' }}">
                        <i class="bi bi-ticket-detailed-fill"></i> Tiket Perbaikan
                    </a>
                </li>
                @if(in_array(Auth::user()->role, ['Supervisor', 'Super Admin']))
                <li class="nav-item">
                    <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.index') || request()->routeIs('reports.export.*') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-bar-graph-fill"></i> Laporan Tiket
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('reports.machine.data') }}" class="nav-link {{ request()->routeIs('reports.machine.*') ? 'active' : '' }}">
                        <i class="bi bi-cpu-fill"></i> Laporan Data Mesin
                    </a>
                </li>
                @endif

                <!-- Master Data -->
                @if(Auth::user()->role === 'Super Admin')
                <li class="nav-section">Master Data</li>
                <li class="nav-item">
                    <a href="{{ route('machines.index') }}" class="nav-link {{ request()->routeIs('machines.*') ? 'active' : '' }}">
                        <i class="bi bi-gear-wide-connected"></i> Data Mesin
                    </a>
                </li>
                @endif

                <!-- Inventaris -->
                @if(in_array(Auth::user()->role, ['Operator', 'Super Admin']))
                <li class="nav-section">Inventaris</li>
                <li class="nav-item">
                    <a href="{{ route('spareparts.index') }}" class="nav-link {{ request()->routeIs('spareparts.*') ? 'active' : '' }}">
                        <i class="bi bi-box-seam-fill"></i> Stok Sparepart
                    </a>
                </li>
                @if(in_array(Auth::user()->role, ['Supervisor', 'Super Admin']))
                <li class="nav-item">
                    <a href="{{ route('reports.sparepart.stock') }}" class="nav-link {{ request()->routeIs('reports.sparepart.*') ? 'active' : '' }}">
                        <i class="bi bi-clipboard-data-fill"></i> Laporan Stok
                    </a>
                </li>
                @endif
                @endif

                <!-- Sistem -->
                @if(Auth::user()->role === 'Super Admin')
                <li class="nav-section">Sistem</li>
                <li class="nav-item">
                    <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <i class="bi bi-people-fill"></i> Manajemen Pengguna
                    </a>
                </li>
                @endif
            </ul>
        </nav>
    </aside>

    <!-- Main Content Area -->
    <div class="main-content">
        <!-- Top Navbar -->
        <header class="top-navbar">
            <div class="left-section">
                <button class="btn btn-light d-md-none me-2 border-0" id="toggleSidebarBtn" style="padding:4px 8px;">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <h5 class="d-none d-md-block">@yield('title', 'Dashboard')</h5>
                <h5 class="d-md-none fw-bold" style="font-size:1rem;">MMS</h5>
            </div>

            <div class="right-section">
                <!-- Notification -->
                <a href="#" class="notification-btn">
                    <i class="bi bi-bell fs-5"></i>
                    <span class="badge-dot"></span>
                </a>

                <!-- Profile Dropdown -->
                <div class="dropdown">
                    <a href="#" class="navbar-profile dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                        @if(Auth::user()->avatar)
                            <img src="{{ asset('storage/avatars/' . Auth::user()->avatar) }}" alt="Avatar">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=0D8ABC&color=fff" alt="Avatar">
                        @endif
                        <div class="profile-text d-none d-md-block">
                            <div class="name">{{ Auth::user()->name }}</div>
                            <div class="role">{{ Auth::user()->role }}</div>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow mt-2" style="min-width: 200px;">
                        <li class="px-3 py-2 border-bottom">
                            <div class="fw-semibold text-dark" style="font-size:0.85rem;">{{ Auth::user()->name }}</div>
                            <div class="text-muted" style="font-size:0.75rem;">{{ Auth::user()->email }}</div>
                        </li>
                        <li><a class="dropdown-item py-2" href="{{ route('profile.show') }}"><i class="bi bi-person me-2"></i> Profil Saya</a></li>
                        <li><a class="dropdown-item py-2" href="{{ route('profile.password') }}"><i class="bi bi-key me-2"></i> Ubah Password</a></li>
                        <li><a class="dropdown-item py-2" href="{{ route('profile.photo') }}"><i class="bi bi-camera me-2"></i> Ubah Foto</a></li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item py-2 text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <div class="p-4 flex-grow-1">
            @yield('content')
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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