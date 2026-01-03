<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="/" class="brand-link">
        <img src="{{ asset('img/logo_tms.png') }}" alt="avatar" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">TMS CHAT</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                @php
                    // Detect active guard based on current route
                    $user = null;
                    $activeGuard = null;
                    
                    if (request()->is('admin*')) {
                        // Route admin - gunakan guard admin
                        if (auth('admin')->check()) {
                            $user = auth('admin')->user();
                            $activeGuard = 'admin';
                        }
                    } elseif (request()->is('cs*')) {
                        // Route cs - gunakan guard cs
                        if (auth('cs')->check()) {
                            $user = auth('cs')->user();
                            $activeGuard = 'cs';
                        }
                    } elseif (request()->is('member*') || request()->is('dashboard') || request()->is('chat*') || request()->is('room_chat')) {
                        // Route member - gunakan guard member
                        if (auth('member')->check()) {
                            $user = auth('member')->user();
                            $activeGuard = 'member';
                        }
                    }
                    
                    // Fallback: check all guards if route pattern doesn't match
                    if (!$user) {
                        if (auth('admin')->check()) {
                            $user = auth('admin')->user();
                            $activeGuard = 'admin';
                        } elseif (auth('cs')->check()) {
                            $user = auth('cs')->user();
                            $activeGuard = 'cs';
                        } elseif (auth('member')->check()) {
                            $user = auth('member')->user();
                            $activeGuard = 'member';
                        }
                    }
                    
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
                @if ($user && $user->role === 'admin')
                    <li class="nav-header">MENU ADMIN</li>
                    
                    <li class="nav-item">
                        <a href="/admin/dashboard"
                            class="nav-link {{ request()->is('admin/dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/admin/dataakuns" class="nav-link {{ request()->is('admin/dataakuns*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Data Akun</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/admin/sesi-chat"
                            class="nav-link {{ request()->is('admin/sesi-chat*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-comments"></i>
                            <p>Semua Sesi Chat</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/admin/rating"
                            class="nav-link {{ request()->is('admin/rating*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-star"></i>
                            <p>Data Rating</p>
                        </a>
                    </li>

                    <li class="nav-header">MENU CS</li>
                    
                    <li class="nav-item">
                        <a href="/admin/cs/chat" class="nav-link {{ request()->is('admin/cs/chat*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-comments"></i>
                            <p>Semua Chat</p>
                        </a>
                    </li>
                @endif

                {{-- untuk cs --}}
                @if ($user && $user->role === 'cs')
                    <li class="nav-header">MENU CS</li>
                    
                    <li class="nav-item">
                        <a href="/cs/chat" class="nav-link {{ request()->is('cs/chat') && !request()->has('filter') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-comments"></i>
                            <p>Semua Chat</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/cs/chat?filter=my-chats"
                            class="nav-link {{ request()->is('cs/chat') && request()->get('filter') === 'my-chats' ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-circle"></i>
                            <p>Chat Saya</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/cs/profile"
                            class="nav-link {{ request()->is('cs/profile') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user"></i>
                            <p>Profil</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/cs/rating"
                            class="nav-link {{ request()->is('cs/rating*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-star"></i>
                            <p>Ratting Saya</p>
                        </a>
                    </li>
                @endif

                {{-- untuk member --}}
                @if ($user && (!isset($user->role) || $user->role === 'member'))
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
