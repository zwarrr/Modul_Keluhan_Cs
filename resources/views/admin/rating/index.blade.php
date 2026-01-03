<x-layouts.app>
    <div class="content">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Data Rating Pelayanan CS</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/admin/dashboard">Home</a></li>
                            <li class="breadcrumb-item active">Data Rating</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                
                <!-- Statistik Cards -->
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ $totalRatings }}</h3>
                                <p>Total Rating Diterima</p>
                            </div>
                            <div class="icon"><i class="fas fa-star"></i></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>{{ number_format($averageRating, 1) }}</h3>
                                <p>Rating Rata-rata</p>
                            </div>
                            <div class="icon"><i class="fas fa-chart-line"></i></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>{{ $ratingDistribution[5] ?? 0 }}</h3>
                                <p>Rating Bintang 5</p>
                            </div>
                            <div class="icon"><i class="fas fa-star"></i></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3>{{ ($ratingDistribution[1] ?? 0) + ($ratingDistribution[2] ?? 0) }}</h3>
                                <p>Rating Rendah (1-2)</p>
                            </div>
                            <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
                        </div>
                    </div>
                </div>

                <!-- Charts -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-chart-bar mr-2"></i>Distribusi Rating</h3>
                            </div>
                            <div class="card-body">
                                <canvas id="ratingDistributionChart" height="120"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card card-success card-outline">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-trophy mr-2"></i>Performa CS (Top 10)</h3>
                            </div>
                            <div class="card-body">
                                <canvas id="csPerformanceChart" height="120"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter & Table -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Filter</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <form method="GET" action="{{ route('admin.rating.index') }}">
                                    <div class="mb-3">
                                        <label for="filter_cs" class="form-label mb-1">CS</label>
                                        <select name="cs_id" id="filter_cs" class="form-control form-control-sm">
                                            <option value="">Semua CS</option>
                                            @foreach($csList as $cs)
                                                <option value="{{ $cs->id }}" {{ request('cs_id') == $cs->id ? 'selected' : '' }}>
                                                    {{ $cs->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="filter_rating" class="form-label mb-1">Rating</label>
                                        <select name="rating" id="filter_rating" class="form-control form-control-sm">
                                            <option value="">Semua Rating</option>
                                            @for($i = 5; $i >= 1; $i--)
                                                <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>
                                                    {{ $i }} Bintang
                                                </option>
                                            @endfor
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="date_from" class="form-label mb-1">Tanggal Dari</label>
                                        <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" 
                                               value="{{ request('date_from') }}">
                                    </div>

                                    <div class="mb-3">
                                        <label for="date_to" class="form-label mb-1">Tanggal Sampai</label>
                                        <input type="date" name="date_to" id="date_to" class="form-control form-control-sm" 
                                               value="{{ request('date_to') }}">
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-sm btn-block">
                                        <i class="fas fa-filter"></i> Terapkan Filter
                                    </button>
                                    <a href="{{ route('admin.rating.index') }}" class="btn btn-secondary btn-sm btn-block">
                                        <i class="fas fa-redo"></i> Reset
                                    </a>
                                </form>
                            </div>
                        </div>

                        <!-- CS Performance Summary -->
                        <div class="card card-warning card-outline">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-medal mr-2"></i>Ranking CS</h3>
                            </div>
                            <div class="card-body p-0">
                                <ul class="list-group list-group-flush">
                                    @foreach($csPerformance->take(5) as $index => $cs)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>
                                                <span class="badge badge-{{ $index === 0 ? 'warning' : ($index === 1 ? 'secondary' : 'info') }} mr-2">
                                                    #{{ $index + 1 }}
                                                </span>
                                                {{ $cs['name'] }}
                                            </span>
                                            <span class="badge badge-success badge-pill">
                                                <i class="fas fa-star"></i> {{ $cs['avg_rating'] }}
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-9">
                        <div class="card card-danger card-outline">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-table mr-2"></i>Data Rating Pelayanan</h3>
                                <div class="card-tools">
                                    <span class="badge badge-info">{{ $ratings->total() }} data</span>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th style="width: 5%">#</th>
                                                <th style="width: 15%">Sesi ID</th>
                                                <th style="width: 20%">Member</th>
                                                <th style="width: 20%">CS</th>
                                                <th style="width: 10%">Rating</th>
                                                <th style="width: 20%">Tanggal Rating</th>
                                                <th style="width: 10%">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($ratings as $index => $rating)
                                                <tr>
                                                    <td>{{ $ratings->firstItem() + $index }}</td>
                                                    <td>
                                                        <span class="badge badge-secondary">#{{ $rating->id }}</span>
                                                    </td>
                                                    <td>
                                                        <i class="fas fa-user-circle mr-1 text-primary"></i>
                                                        {{ $rating->member->name ?? '-' }}
                                                    </td>
                                                    <td>
                                                        <i class="fas fa-headset mr-1 text-success"></i>
                                                        {{ $rating->cs->name ?? '-' }}
                                                    </td>
                                                    <td>
                                                        @php
                                                            $ratingValue = $rating->rating_pelayanan;
                                                            $badgeClass = $ratingValue >= 4 ? 'success' : ($ratingValue >= 3 ? 'warning' : 'danger');
                                                        @endphp
                                                        <span class="badge badge-{{ $badgeClass }} badge-lg">
                                                            @for($i = 1; $i <= 5; $i++)
                                                                <i class="fas fa-star{{ $i <= $ratingValue ? '' : '-o' }}"></i>
                                                            @endfor
                                                            {{ $ratingValue }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <small>
                                                            <i class="far fa-clock mr-1"></i>
                                                            {{ \Carbon\Carbon::parse($rating->rating_pelayanan_at)->format('d M Y H:i') }}
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('admin.sesi-chat.detail', $rating->id) }}" 
                                                           class="btn btn-sm btn-info" title="Lihat Detail">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted py-4">
                                                        <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                                        Belum ada data rating
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @if($ratings->hasPages())
                                <div class="card-footer clearfix">
                                    <div class="float-right">
                                        {{ $ratings->links() }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script>
    setTimeout(function() {
        console.log('Chart initialization started');
        
        // Data dari backend
        const ratingDistribution = {!! json_encode($ratingDistribution) !!};
        const csPerformance = {!! json_encode($csPerformance) !!};

        console.log('Rating Distribution:', ratingDistribution);
        console.log('CS Performance:', csPerformance);

        // Chart 1: Rating Distribution
        const ctx1 = document.getElementById('ratingDistributionChart');
        console.log('Canvas 1:', ctx1);
        
        if (ctx1) {
            const chart1 = new Chart(ctx1, {
                type: 'bar',
                data: {
                    labels: ['1 Bintang', '2 Bintang', '3 Bintang', '4 Bintang', '5 Bintang'],
                    datasets: [{
                        label: 'Jumlah Rating',
                        data: [
                            parseInt(ratingDistribution[1]) || 0,
                            parseInt(ratingDistribution[2]) || 0,
                            parseInt(ratingDistribution[3]) || 0,
                            parseInt(ratingDistribution[4]) || 0,
                            parseInt(ratingDistribution[5]) || 0
                        ],
                        backgroundColor: [
                            'rgba(220, 53, 69, 0.7)',
                            'rgba(255, 193, 7, 0.7)',
                            'rgba(23, 162, 184, 0.7)',
                            'rgba(40, 167, 69, 0.7)',
                            'rgba(40, 167, 69, 0.9)'
                        ],
                        borderColor: [
                            'rgba(220, 53, 69, 1)',
                            'rgba(255, 193, 7, 1)',
                            'rgba(23, 162, 184, 1)',
                            'rgba(40, 167, 69, 1)',
                            'rgba(40, 167, 69, 1)'
                        ],
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
            console.log('Chart 1 created');
        }

        // Chart 2: CS Performance
        const ctx2 = document.getElementById('csPerformanceChart');
        console.log('Canvas 2:', ctx2);
        
        const csPerformanceArray = Array.isArray(csPerformance) ? csPerformance : Object.values(csPerformance);
        
        if (ctx2 && csPerformanceArray.length > 0) {
            const chart2 = new Chart(ctx2, {
                type: 'bar',
                data: {
                    labels: csPerformanceArray.slice(0, 10).map(cs => cs.name),
                    datasets: [{
                        label: 'Rating Rata-rata',
                        data: csPerformanceArray.slice(0, 10).map(cs => parseFloat(cs.avg_rating) || 0),
                        backgroundColor: 'rgba(40, 167, 69, 0.7)',
                        borderColor: 'rgba(40, 167, 69, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            max: 5,
                            ticks: { stepSize: 0.5 }
                        }
                    }
                }
            });
            console.log('Chart 2 created');
        } else {
            console.warn('No CS performance data');
        }
        
        console.log('Chart initialization completed');
    }, 500);
    </script>

    <script>
        // Auto refresh halaman setiap 10 detik
        setInterval(function() {
            location.reload();
        }, 10000);
    </script>
    
    <style>
        .badge-lg {
            font-size: 0.9rem;
            padding: 0.4rem 0.6rem;
        }
        .list-group-item:hover {
            background-color: #f8f9fa;
        }
    </style>
</x-layouts.app>
