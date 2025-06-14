{{-- Sidebar --}}
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    {{-- Sidebar - Brand --}}
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ url('/admin/dashboard') }}">
        <div class="sidebar-brand-icon">
            {{-- Mengganti ikon Font Awesome dengan tag <img> untuk logo Anda --}}
            <img src="{{ asset('img/bibliobit_aries.png') }}" alt="Bibliobit Logo" style="height: 40px;">
        </div>
        <div class="sidebar-brand-text mx-3">Bibliobit Admin</div>
    </a>

    {{-- Divider --}}
    <hr class="sidebar-divider my-0">

    {{-- Nav Item - Dashboard --}}
    <li class="nav-item {{ Request::is('admin/dashboard*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ url('/admin/dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    {{-- Nav Item - Charts --}}
    <li class="nav-item {{ Request::is('admin/charts*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ url('/admin/charts') }}">
            <i class="fas fa-fw fa-chart-area"></i>
            <span>Charts</span>
        </a>
    </li>

    {{-- Divider --}}
    <hr class="sidebar-divider d-none d-md-block">

    {{-- Sidebar Toggler (Sidebar) --}}
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
{{-- End of Sidebar --}}
