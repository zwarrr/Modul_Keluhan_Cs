<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="index3.html" class="nav-link">Home</a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="#" class="nav-link">Contact</a>
        </li>
    </ul>

    <!-- SEARCH FORM 
    <form class="form-inline ml-3">
        <div class="input-group input-group-sm">
            <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
            <div class="input-group-append">
                <button class="btn btn-navbar" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </form>
    -->

    <!-- Right navbar links -->
        <!-- Profile Info -->
        @php
            // Detect active guard based on current route
            $user = null;
            
            if (request()->is('admin*')) {
                // Route admin - gunakan guard admin saja
                if (auth('admin')->check()) {
                    $user = auth('admin')->user();
                }
            } elseif (request()->is('cs*')) {
                // Route cs - gunakan guard cs saja
                if (auth('cs')->check()) {
                    $user = auth('cs')->user();
                }
            } elseif (request()->is('member*') || request()->is('dashboard') || request()->is('chat*') || request()->is('room_chat')) {
                // Route member - gunakan guard member saja
                if (auth('member')->check()) {
                    $user = auth('member')->user();
                }
            }
            
            // Fallback: check all guards if route pattern doesn't match
            if (!$user) {
                if (auth('admin')->check()) {
                    $user = auth('admin')->user();
                } elseif (auth('cs')->check()) {
                    $user = auth('cs')->user();
                } elseif (auth('member')->check()) {
                    $user = auth('member')->user();
                }
            }
            
            function getInitials($name) {
                $words = explode(' ', trim($name));
                $initials = '';
                foreach ($words as $w) {
                    if ($w !== '') $initials .= strtoupper(mb_substr($w, 0, 1));
                }
                return mb_substr($initials, 0, 2);
            }
        @endphp
        <ul class="navbar-nav align-items-center ml-auto" style="margin-left:auto !important;">
            <li class="nav-item dropdown">
                <a class="nav-link d-flex align-items-center" data-toggle="dropdown" href="#" style="gap:8px;">
                   <!-- @if($user && !empty($user->foto_profile))
                        <img src="{{ asset('storage/'.$user->foto_profile) }}" alt="Profile" class="rounded-circle" style="width:32px;height:32px;object-fit:cover;"> 
                    @else 
                        <span class="d-inline-flex justify-content-center align-items-center rounded-circle bg-primary text-white" style="width:32px;height:32px;font-weight:bold;font-size:1rem;">
                            {{ $user ? getInitials($user->name) : '?' }}
                        </span> -->
                    @endif
                    <span class="ml-2">{{ $user ? $user->name : 'Guest' }}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <span class="dropdown-item-text font-weight-bold">{{ $user ? $user->name : '' }}</span>
                    <span class="dropdown-item-text text-muted small">{{ $user ? $user->email : '' }}</span>
                    <div class="dropdown-divider"></div>
                    <a href="{{ route('logout') }}" class="dropdown-item">Logout</a>
                </div>
            </li>
        </ul>
    {{-- <ul class="navbar-nav ml-auto">
        <!-- Messages Dropdown Menu -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-comments"></i>
                <span class="badge badge-danger navbar-badge">3</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <a href="#" class="dropdown-item">
                    <!-- Message Start -->
                    <div class="media">
                        <img src="dist/img/user1-128x128.jpg" alt="User Avatar" class="img-size-50 mr-3 img-circle">
                        <div class="media-body">
                            <h3 class="dropdown-item-title">
                                Brad Diesel
                                <span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span>
                            </h3>
                            <p class="text-sm">Call me whenever you can...</p>
                            <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
                        </div>
                    </div>
                    <!-- Message End -->
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <!-- Message Start -->
                    <div class="media">
                        <img src="dist/img/user8-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
                        <div class="media-body">
                            <h3 class="dropdown-item-title">
                                John Pierce
                                <span class="float-right text-sm text-muted"><i class="fas fa-star"></i></span>
                            </h3>
                            <p class="text-sm">I got your message bro</p>
                            <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
                        </div>
                    </div>
                    <!-- Message End -->
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <!-- Message Start -->
                    <div class="media">
                        <img src="dist/img/user3-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
                        <div class="media-body">
                            <h3 class="dropdown-item-title">
                                Nora Silvester
                                <span class="float-right text-sm text-warning"><i class="fas fa-star"></i></span>
                            </h3>
                            <p class="text-sm">The subject goes here</p>
                            <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
                        </div>
                    </div>
                    <!-- Message End -->
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item dropdown-footer">See All Messages</a>
            </div>
        </li>
        <!-- Notifications Dropdown Menu -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-bell"></i>
                <span class="badge badge-warning navbar-badge">15</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-item dropdown-header">15 Notifications</span>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-envelope mr-2"></i> 4 new messages
                    <span class="float-right text-muted text-sm">3 mins</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-users mr-2"></i> 8 friend requests
                    <span class="float-right text-muted text-sm">12 hours</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-file mr-2"></i> 3 new reports
                    <span class="float-right text-muted text-sm">2 days</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#">
                <i class="fas fa-th-large"></i>
            </a>
        </li>
    </ul> --}}
</nav>
<!-- /.navbar -->
