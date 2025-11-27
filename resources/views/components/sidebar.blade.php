<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="/" class="brand-link">
        <img src="https://i.pinimg.com/736x/47/c7/94/47c794ea16e203917795234981edf120.jpg"
            alt="APP CS Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">APP CS</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                @php
                    $user = auth()->user();
                    $foto = $user->foto_profile ?? null;
                    $name = $user->name ?? '';
                    $initial = collect(explode(' ', $name))
                        ->map(fn($n) => strtoupper(Str::substr($n, 0, 1)))
                        ->implode('');
                    if (strlen($initial) > 2) {
                        $initial = substr($initial, 0, 2);
                    }
                    $bgColors = ['#007bff', '#28a745', '#dc3545', '#ffc107', '#17a2b8', '#6f42c1', '#fd7e14'];
                    $bg = $bgColors[ord($initial[0] ?? 'A') % count($bgColors)];
                @endphp
                @if ($foto)
                    <img src="{{ asset('storage/' . $foto) }}" class="img-circle elevation-2" alt="User Image"
                        style="object-fit:cover;width:40px;height:40px;">
                @else
                    <div class="img-circle elevation-2 d-flex align-items-center justify-content-center"
                        style="width:40px;height:40px;background:{{ $bg }};color:#fff;font-weight:bold;font-size:1.2rem;">
                        {{ $initial }}
                    </div>
                @endif
            </div>
            <div class="info">
                <a href="#" class="d-block">{{ $user->name }}</a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->

               {{-- untuk admin --}}
                @if (auth()->user()->role === 'admin')
                    <li class="nav-item">
                        <a href="/admin/dashboard"
                            class="nav-link {{ request()->is('admin/dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>

                    <li class="nav-header">MANAGEMENT USER</li>

                    <li class="nav-item">
                        <a href="/admin/members" class="nav-link {{ request()->is('admin/members') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Data Member</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/admin/costumer-services" class="nav-link {{ request()->is('admin/customer-services') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-headset"></i>
                            <p>Data Customer Service</p>
                        </a>
                    </li>

                    <li class="nav-header">CHAT & KOMPLAIN</li>

                    <li class="nav-item">
                        <a href="/admin/chat-sesi"
                            class="nav-link {{ request()->is('admin/chat-sesi') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-comments"></i>
                            <p>Semua Chat Sesi</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/admin/chat-monitor"
                            class="nav-link {{ request()->is('admin/chat-monitor') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-eye"></i>
                            <p>Monitoring Chat Berjalan</p>
                        </a>
                    </li>

                    <li class="nav-header">LAPORAN</li>

                    <li class="nav-item">
                        <a href="/admin/report" class="nav-link {{ request()->is('admin/report') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-file-alt"></i>
                            <p>Rekap Laporan</p>
                        </a>
                    </li>

                    <li class="nav-header">PENGATURAN</li>

                    <li class="nav-item">
                        <a href="/admin/settings"
                            class="nav-link {{ request()->is('admin/settings') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-cog"></i>
                            <p>Pengaturan Sistem</p>
                        </a>
                    </li>
                @endif

                {{-- untuk cs --}}
                @if (auth()->user()->role === 'cs')
                    <li class="nav-item">
                        <a href="/cs/dashboard" class="nav-link {{ request()->is('cs/dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard CS</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/cs/chat-aktif" class="nav-link {{ request()->is('cs/chat-aktif') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-comments"></i>
                            <p>Chat Aktif</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/cs/chat-riwayat"
                            class="nav-link {{ request()->is('cs/chat-riwayat') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-history"></i>
                            <p>Riwayat Chat</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/cs/rating" class="nav-link {{ request()->is('cs/rating') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-star"></i>
                            <p>Rating & Evaluasi</p>
                        </a>
                    </li>
                @endif

                {{-- untuk member --}}
                @if (auth()->user()->role === 'member')
                    <li class="nav-item">
                        <a href="/dashboard"
                            class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-home"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/chat" class="nav-link {{ request()->is('chat') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-comments"></i>
                            <p>Chat Komplain</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/chat-history"
                            class="nav-link {{ request()->is('chat-history') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-history"></i>
                            <p>Riwayat Komplain</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/profile"
                            class="nav-link {{ request()->is('profile') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user"></i>
                            <p>Profile Saya</p>
                        </a>
                    </li>
                @endif

            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
