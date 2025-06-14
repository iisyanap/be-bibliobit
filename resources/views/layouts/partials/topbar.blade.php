{{-- Topbar --}}
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    {{-- Sidebar Toggle (Topbar) --}}
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    {{-- Topbar Navbar --}}
    <ul class="navbar-nav ml-auto">

        {{-- Nav Item - User Information --}}
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                {{-- PERBAIKAN: Menampilkan nama user yang sedang login --}}
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{ Auth::user()->name }}</span>

                {{-- Mengambil inisial dari nama untuk placeholder --}}
                <img class="img-profile rounded-circle"
                    src="https://placehold.co/60x60/55AD9B/FFFFFF?text={{ substr(Auth::user()->name, 0, 1) }}" alt="User Profile">
            </a>

            {{-- Dropdown - User Information --}}
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                aria-labelledby="userDropdown">

                {{-- PERBAIKAN: Tombol logout yang fungsional --}}
                <a class="dropdown-item" href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    Logout
                </a>

                {{-- Form logout yang tersembunyi, diperlukan oleh Laravel --}}
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </li>

    </ul>

</nav>
{{-- End of Topbar --}}
