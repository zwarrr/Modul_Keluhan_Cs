<x-layouts.app>
    <div class="content">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Dashboard</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <!-- 1. Ringkasan Statistik (Card Summary) -->
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ $complainsToday }}</h3>
                                <p>Total Komplain Hari Ini</p>
                            </div>
                            <div class="icon"><i class="fas fa-calendar-day"></i></div>
                            <a href="{{ route('admin.sesi-chat.index') }}" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h3>{{ $complainsThisMonth }}</h3>
                                <p>Total Komplain Bulan Ini</p>
                            </div>
                            <div class="icon"><i class="fas fa-calendar-alt"></i></div>
                            <a href="{{ route('admin.sesi-chat.index') }}" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>{{ $complainsOpen }}</h3>
                                <p>Komplain Status Open</p>
                            </div>
                            <div class="icon"><i class="fas fa-folder-open"></i></div>
                            <a href="{{ route('admin.sesi-chat.index') }}?status=open" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-secondary">
                            <div class="inner">
                                <h3>{{ $complainsPending }}</h3>
                                <p>Komplain Pending</p>
                            </div>
                            <div class="icon"><i class="fas fa-spinner"></i></div>
                            <a href="{{ route('admin.sesi-chat.index') }}?status=pending" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>{{ $complainsClosed }}</h3>
                                <p>Komplain Closed/Selesai</p>
                            </div>
                            <div class="icon"><i class="fas fa-check-circle"></i></div>
                            <a href="{{ route('admin.sesi-chat.index') }}?status=closed" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-teal">
                            <div class="inner">
                                <h3>{{ $totalMembers }}</h3>
                                <p>Member Terdaftar</p>
                            </div>
                            <div class="icon"><i class="fas fa-users"></i></div>
                            <a href="{{ route('dataakuns.index') }}" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-purple">
                            <div class="inner">
                                <h3>{{ $totalCS }}</h3>
                                <p>CS Aktif</p>
                            </div>
                            <div class="icon"><i class="fas fa-headset"></i></div>
                            <a href="{{ route('dataakuns.index') }}" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                </div>

                <!-- 2. Grafik / Chart -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-chart-line mr-2"></i>Grafik Komplain 7 Hari Terakhir</h3>
                            </div>
                            <div class="card-body">
                                <canvas id="chart-komplain-harian" height="120"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card card-info card-outline">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-chart-bar mr-2"></i>Grafik Komplain 6 Bulan Terakhir</h3>
                            </div>
                            <div class="card-body">
                                <canvas id="chart-komplain-bulanan" height="120"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card card-warning card-outline">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-star mr-2"></i>Grafik Rating CS (Top 10)</h3>
                            </div>
                            <div class="card-body">
                                <canvas id="chart-rating-cs" height="120"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card card-success card-outline">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-trophy mr-2"></i>Top 5 CS (Komplain Selesai)</h3>
                            </div>
                            <div class="card-body">
                                @if($topCS->count() > 0)
                                    <ul class="list-group">
                                        @foreach($topCS as $index => $cs)
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span>
                                                    <span class="badge badge-{{ $index === 0 ? 'warning' : ($index === 1 ? 'secondary' : 'bronze') }} mr-2">
                                                        #{{ $index + 1 }}
                                                    </span>
                                                    {{ $cs->name }}
                                                </span>
                                                <span class="badge badge-success badge-pill">{{ $cs->closed_count }} komplain</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-muted text-center">Belum ada data</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Daftar Komplain Terbaru -->
                <div class="card card-danger card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-comments mr-2"></i>Daftar Komplain Terbaru</h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.sesi-chat.index') }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-list"></i> Lihat Semua
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 20%">Member</th>
                                    <th style="width: 20%">CS</th>
                                    <th style="width: 15%">Status</th>
                                    <th style="width: 25%">Last Message</th>
                                    <th style="width: 15%">Last Activity</th>
                                    <th style="width: 5%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentComplains as $session)
                                    <tr>
                                        <td>
                                            <i class="fas fa-user-circle mr-1"></i>
                                            {{ $session->member->name ?? '-' }}
                                        </td>
                                        <td>
                                            @if($session->cs)
                                                <i class="fas fa-headset mr-1"></i>
                                                {{ $session->cs->name }}
                                            @else
                                                <span class="text-muted"><i>Belum ditangani</i></span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($session->status === 'open')
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-folder-open"></i> Open
                                                </span>
                                            @elseif($session->status === 'pending')
                                                <span class="badge badge-secondary">
                                                    <i class="fas fa-spinner"></i> Pending
                                                </span>
                                            @else
                                                <span class="badge badge-success">
                                                    <i class="fas fa-check-circle"></i> Closed
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ Str::limit($session->last_message ?? 'Belum ada pesan', 40) }}
                                            </small>
                                        </td>
                                        <td>
                                            <small>
                                                {{ \Carbon\Carbon::parse($session->last_activity)->diffForHumans() }}
                                            </small>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.sesi-chat.detail', $session->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">Belum ada komplain</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 4. Daftar CS Online / Offline -->
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-users-cog mr-2"></i>Daftar CS</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-bordered table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 40%">Nama CS</th>
                                    <th style="width: 20%" class="text-center">Komplain Aktif</th>
                                    <th style="width: 20%" class="text-center">Komplain Selesai</th>
                                    <th style="width: 20%" class="text-center">Rating Rata-rata</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($csList as $cs)
                                    <tr>
                                        <td>
                                            <i class="fas fa-user-tie mr-2 text-primary"></i>
                                            <strong>{{ $cs->name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $cs->email }}</small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-{{ $cs->active_count > 0 ? 'info' : 'secondary' }} badge-lg">
                                                {{ $cs->active_count }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $closedCount = $cs->sessions()->where('status', 'closed')->count();
                                            @endphp
                                            <span class="badge badge-success badge-lg">
                                                {{ $closedCount }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @if($cs->avg_rating !== '-')
                                                <span class="badge badge-warning badge-lg">
                                                    <i class="fas fa-star"></i> {{ $cs->avg_rating }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Belum ada CS</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 6. Menu Akses Cepat (Quick Actions) -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-secondary">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-bolt mr-2"></i>Akses Cepat</h3>
                            </div>
                            <div class="card-body">
                                <a href="{{ route('dataakuns.create') }}" class="btn btn-primary mb-2">
                                    <i class="fas fa-user-plus"></i> Tambah Akun
                                </a>
                                <a href="{{ route('admin.sesi-chat.index') }}" class="btn btn-info mb-2">
                                    <i class="fas fa-list"></i> Lihat Semua Komplain
                                </a>
                                <a href="{{ route('admin.sesi-chat.index') }}?status=open" class="btn btn-warning mb-2">
                                    <i class="fas fa-folder-open"></i> Lihat Komplain Open
                                </a>
                                <a href="{{ route('admin.cs.chat.index') }}" class="btn btn-danger mb-2">
                                    <i class="fas fa-exclamation-circle"></i> Lihat Komplain Belum Ditangani
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script>
    setTimeout(function() {
        console.log('Dashboard chart initialization started');
        
        // Data dari backend
        const last7DaysData = @json($last7Days);
        const last6MonthsData = @json($last6Months);
        const ratingData = @json($ratingData);

        console.log('Last 7 Days Data:', last7DaysData);
        console.log('Last 6 Months Data:', last6MonthsData);
        console.log('Rating Data:', ratingData);

        // Chart 1: Komplain 7 Hari Terakhir
        const ctx1 = document.getElementById('chart-komplain-harian');
        console.log('Canvas 1:', ctx1);
        if (ctx1) {
            new Chart(ctx1, {
            type: 'line',
            data: {
                labels: last7DaysData.map(d => d.date),
                datasets: [{
                    label: 'Jumlah Komplain',
                    data: last7DaysData.map(d => parseInt(d.count) || 0),
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
        console.log('Chart 1 created');
        }

        // Chart 2: Komplain per Bulan
        const ctx2 = document.getElementById('chart-komplain-bulanan');
        console.log('Canvas 2:', ctx2);
        if (ctx2) {
            new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: last6MonthsData.map(d => d.month),
                datasets: [{
                    label: 'Jumlah Komplain',
                    data: last6MonthsData.map(d => parseInt(d.count) || 0),
                    backgroundColor: 'rgba(23, 162, 184, 0.7)',
                    borderColor: 'rgba(23, 162, 184, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
        console.log('Chart 2 created');
        }

        // Chart 3: Rating CS
        const ctx3 = document.getElementById('chart-rating-cs');
        console.log('Canvas 3:', ctx3);
        if (ctx3) {
            new Chart(ctx3, {
            type: 'bar',
            data: {
                labels: ratingData.map(d => d.name),
                datasets: [{
                    label: 'Rating',
                    data: ratingData.map(d => parseFloat(d.rating) || 0),
                    backgroundColor: 'rgba(255, 193, 7, 0.7)',
                    borderColor: 'rgba(255, 193, 7, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 5,
                        ticks: { stepSize: 0.5 }
                    }
                }
            }
        });
        console.log('Chart 3 created');
        }
        
        console.log('Dashboard chart initialization completed');
    }, 500);
    </script>

    <script>
        // Auto refresh dashboard every 10 seconds
        setInterval(function() {
            location.reload();
        }, 10000);
    </script>

    <style>
        .badge-bronze {
            background-color: #cd7f32;
            color: white;
        }
        .badge-lg {
            font-size: 1rem;
            padding: 0.5rem 0.75rem;
        }
        .small-box .icon {
            font-size: 70px;
        }
        .small-box-footer {
            display: block;
            padding: 3px 0;
            text-align: center;
            text-decoration: none;
            color: rgba(255, 255, 255, 0.8);
        }
        .small-box-footer:hover {
            color: #fff;
            background: rgba(0, 0, 0, 0.1);
        }
        .list-group-item {
            border-left: 3px solid transparent;
        }
        .list-group-item:hover {
            background-color: #f8f9fa;
            border-left-color: #007bff;
        }
    </style>
</x-layouts.app>
