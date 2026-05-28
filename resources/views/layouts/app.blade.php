<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIGVI') – Install D</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- SIGVI Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/sigvi.css') }}">

    @stack('styles')
</head>
<body>
<div class="layout-wrapper">

    <!-- ===================== SIDEBAR ===================== -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    <aside class="sidebar" id="sidebar">
        <!-- Brand -->
        <div class="sidebar-brand">
            <img src="{{ asset('assets/img/Logo/Logo_Install D.jpeg') }}"
                 alt="Logo"
                 style="width: 44px; height: 44px; border-radius: 12px; object-fit: cover; box-shadow: 0 2px 8px rgba(0,0,0,0.15);">
            <div class="brand-text">
                <span class="brand-name">Install D</span>
                <span class="brand-sub">SIGVI · Ventas &amp; Inventario</span>
            </div>
            <button class="btn-sidebar-close d-lg-none" onclick="closeSidebar()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="sidebar-nav">

            <div class="nav-label">Principal</div>
            <a href="{{ route('dashboard') }}"
               class="nav-link-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <span class="nav-icon"><i class="bi bi-speedometer2"></i></span>
                <span>Dashboard</span>
            </a>

            <div class="nav-label">Ventas</div>
            <a href="{{ route('ventas.index') }}"
               class="nav-link-item {{ request()->routeIs('ventas.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="bi bi-receipt-cutoff"></i></span>
                <span>Ventas</span>
            </a>
            <a href="{{ route('clientes.index') }}"
               class="nav-link-item {{ request()->routeIs('clientes.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="bi bi-people-fill"></i></span>
                <span>Clientes</span>
            </a>
            <a href="{{ route('garantias.index') }}"
               class="nav-link-item {{ request()->routeIs('garantias.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="bi bi-shield-check"></i></span>
                <span>Garantías</span>
            </a>

            <div class="nav-label">Inventario</div>
            <a href="{{ route('productos.index') }}"
               class="nav-link-item {{ request()->routeIs('productos.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="bi bi-boxes"></i></span>
                <span>Productos</span>
            </a>
            <a href="{{ route('compras.index') }}"
               class="nav-link-item {{ request()->routeIs('compras.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="bi bi-cart-plus-fill"></i></span>
                <span>Compras</span>
            </a>
            <a href="{{ route('proveedores.index') }}"
               class="nav-link-item {{ request()->routeIs('proveedores.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="bi bi-truck-front-fill"></i></span>
                <span>Proveedores</span>
            </a>

            <div class="nav-label">Catálogo</div>
            <a href="{{ route('categorias.index') }}"
               class="nav-link-item {{ request()->routeIs('categorias.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="bi bi-tag-fill"></i></span>
                <span>Categorías</span>
            </a>
            <a href="{{ route('marcas.index') }}"
               class="nav-link-item {{ request()->routeIs('marcas.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="bi bi-bookmark-star-fill"></i></span>
                <span>Marcas</span>
            </a>

            <div class="nav-label">Configuración</div>
            <a href="{{ route('empresa.index') }}"
               class="nav-link-item {{ request()->routeIs('empresa.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="bi bi-building-fill-gear"></i></span>
                <span>Empresa</span>
            </a>
            <a href="{{ route('series.index') }}"
               class="nav-link-item {{ request()->routeIs('series.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="bi bi-123"></i></span>
                <span>Series</span>
            </a>
        </nav>

        <!-- User footer -->
        <div class="sidebar-footer">
            <div class="user-avatar-sm">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
            <div class="user-details">
                <span class="user-name">{{ Auth::user()->name }}</span>
                <span class="user-role">Administrador</span>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn-logout" title="Cerrar sesión">
                    <i class="bi bi-box-arrow-right"></i>
                </button>
            </form>
        </div>
    </aside>
    <!-- ==================== /SIDEBAR ==================== -->

    <!-- ==================== MAIN CONTENT ==================== -->
    <div class="main-content" id="mainContent">

        <!-- Topbar -->
        <div class="topbar">
            <div class="topbar-left">
                <button class="btn-menu-toggle" onclick="openSidebar()">
                    <i class="bi bi-list"></i>
                </button>
                <div class="topbar-title">
                    <h2>@yield('page-icon') @yield('title', 'Dashboard')</h2>
                    <span class="breadcrumb-sub d-none d-sm-block">@yield('subtitle')</span>
                </div>
            </div>
            <div class="topbar-actions">
                @yield('topbar-actions')
            </div>
        </div>

        <!-- Flash Messages -->
        <div class="px-4 pt-3">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-x-circle-fill me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Por favor corrige los siguientes errores:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>

        <!-- Page Content -->
        <div class="page-content">
            @yield('content')
        </div>
    </div>
    <!-- ==================== /MAIN CONTENT ==================== -->

</div><!-- /.layout-wrapper -->

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- SIGVI app.js -->
<script src="{{ asset('js/sigvi.js') }}"></script>

@stack('scripts')
</body>
</html>
