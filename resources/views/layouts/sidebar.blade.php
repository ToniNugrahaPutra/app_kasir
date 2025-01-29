@php
    $outlet = App\Models\Outlet::find(session('outlet_id'));
@endphp
<div id="app-sidepanel" class="app-sidepanel {{ request()->routeIs('transaction.create') ? 'sidepanel-hidden' : 'sidepanel-visible' }}">
    <div id="sidepanel-drop" class="sidepanel-drop"></div>
    <div class="sidepanel-inner d-flex flex-column" id="sidebar">
        <a href="javascript:;" id="sidepanel-close" class="sidepanel-close">
            <i class="fa-solid fa-xmark"></i>
        </a>
        <div class="app-branding">
            <a class="app-logo d-flex align-items-center" href="/">
                <img class="logo-icon" src="{{ $outlet->logo ? asset('storage/logo-toko/'.$outlet->logo) : asset('images/logo-toko.png') }}" alt="logo">
                <h5 class="m-0 p-3 text-white">{{ $outlet->name }}</h5>
            </a>
        </div>
        <nav id="app-nav-main" class="app-nav app-nav-main flex-grow-1">
            <ul class="app-menu list-unstyled accordion">
                {{-- dashboard  --}}
                <li class="nav-item">
                    <a class="nav-link {{ Request::is("/") ? 'active' : '' }}" href="{{ route('home') }}">
                        <span class="nav-icon">
                            <i class="fa-solid fa-house-chimney"></i>
                        </span>
                        <span class="nav-link-text">Dashboard</span>
                    </a>
                </li>

                @role('owner')
                <li class="nav-item has-submenu">
                    <a class="nav-link submenu-toggle {{ request()->routeIs('user.index') ? 'active' : (request()->routeIs('user.create') ? 'active' : '') }}" data-bs-toggle="collapse" data-bs-target="#submenu-3" aria-expanded="{{ request()->routeIs('user.index') ? 'true' : (request()->routeIs('user.create') ? 'true' : 'false') }}" aria-controls="submenu-3" role="button">
                        <span class="nav-icon">
                            <i class="fa-solid fa-user"></i>
                        </span>
                        <span class="nav-link-text">Karyawan</span>
                        <span class="submenu-arrow">
                            <i class="fa-solid fa-chevron-down arrow"></i>
                        </span>
                    </a>
                    <div id="submenu-3" class="submenu submenu-3 {{ request()->routeIs('user.index') ? 'collapse show' : (request()->routeIs('user.create') ? 'collapse show' : 'collapse') }}" data-bs-parent="#menu-accordion">
                        <ul class="submenu-list list-unstyled">
                            <li class="submenu-item"><a class="submenu-link {{ request()->routeIs('user.index') ? 'active' :  ''}}" href="{{ route('user.index') }}">All Karyawan</a></li>
                            <li class="submenu-item"><a class="submenu-link {{ request()->routeIs('user.create') ? 'active' :  ''}}" href="{{ route('user.create') }}">Tambah Karyawan</a></li>
                        </ul>
                    </div>
                </li>
                @endrole

                {{-- menu --}}
                @role('owner')
                <li class="nav-item has-submenu">
                    <a class="nav-link submenu-toggle {{ request()->routeIs('menu.index') ? 'active' : (request()->routeIs('menu.create') ? 'active' : '') }}" data-bs-toggle="collapse" data-bs-target="#submenu-1" aria-expanded="{{ request()->routeIs('menu.index') ? 'true' : (request()->routeIs('menu.create') ? 'true' : 'false') }}" aria-controls="submenu-1" role="button">
                        <span class="nav-icon">
                            <i class="fa-solid fa-bag-shopping"></i>
                        </span>
                        <span class="nav-link-text">Menu</span>
                        <span class="submenu-arrow">
                            <i class="fa-solid fa-chevron-down arrow"></i>
                        </span>
                    </a>
                    <div id="submenu-1" class="submenu submenu-1 {{ request()->routeIs('menu.index') ? 'collapse show' : ( request()->routeIs('menu.create') ? 'collapse show' : 'collapse') }}" data-bs-parent="#menu-accordion">
                        <ul class="submenu-list list-unstyled">
                            <li class="submenu-item"><a class="submenu-link {{ request()->routeIs('menu.index') ? 'active' :  ''}}" href="{{ route('menu.index') }}">Semua Menu</a></li>
                            <li class="submenu-item"><a class="submenu-link {{ request()->routeIs('menu.create') ? 'active' :  ''}}" href="{{ route('menu.create') }}">Tambah Menu</a></li>
                        </ul>
                    </div>
                </li>
                @endrole

                {{-- transaction  --}}
                @if(Auth::user()->hasRole('owner') || Auth::user()->hasRole('cashier'))
                <li class="nav-item has-submenu">
                    <a class="nav-link submenu-toggle {{ request()->routeIs('transaction.index') ? 'active' : (request()->routeIs('transaction.create') ? 'active' : '') }}" data-bs-toggle="collapse" data-bs-target="#submenu-2" aria-expanded="{{ request()->routeIs('transaction.index') ? 'true' : (request()->routeIs('transaction.create') ? 'true' : 'false') }}" aria-controls="submenu-2" role="button">
                        <span class="nav-icon">
                            <i class="fa-solid fa-dollar-sign"></i>
                        </span>
                        <span class="nav-link-text">Transaksi</span>
                        <span class="submenu-arrow">
                            <i class="fa-solid fa-chevron-down arrow"></i>
                        </span>
                    </a>
                    <div id="submenu-2" class="collapse submenu submenu-2 {{ request()->routeIs('transaction.index') ? 'collapse show' : (request()->routeIs('transaction.create') ? 'collapse show' : 'collapse') }}" data-bs-parent="#menu-accordion">
                        <ul class="submenu-list list-unstyled">
                            @if(Auth::user()->hasRole('owner') || Auth::user()->hasRole('cashier'))
                            <li class="submenu-item"><a class="submenu-link {{ request()->routeIs('transaction.index') ? 'active' :  ''}}" href="{{ route('transaction.index') }}">Semua Transaksi</a></li>
                            <li class="submenu-item"><a class="submenu-link {{ request()->routeIs('transaction.create') ? 'active' :  ''}}" href="{{ route('transaction.create') }}">Buat Pesanan</a></li>
                            @endif
                        </ul>
                    </div>
                </li>
                @endif

                <!-- {{-- reports --}}
                @role('owner')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('report') ? 'active' : '' }}" href="{{ route('report') }}">
                            <span class="nav-icon">
                            <i class="fa-solid fa-chart-line"></i></span>
                            <span class="nav-link-text">Laporan Keuntungan</span>
                        </a>
                    </li>
                @endrole -->

                {{-- activityLog --}}
                @role('owner')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('activityLog') ? 'active' : '' }}" href="{{ route('activityLog') }}">
                        <span class="nav-icon">
                            <i class="fa-solid fa-clipboard-list"></i>
                        </span>
                        <span class="nav-link-text">Log Aktivitas</span>
                    </a>
                </li>
                @endrole

                
            </ul>
        </nav>
    </div>
</div>
