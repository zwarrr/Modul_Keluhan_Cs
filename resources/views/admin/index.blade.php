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
                                <h3>12</h3>
                                <p>Total Komplain Hari Ini</p>
                            </div>
                            <div class="icon"><i class="fas fa-calendar-day"></i></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h3>120</h3>
                                <p>Total Komplain Bulan Ini</p>
                            </div>
                            <div class="icon"><i class="fas fa-calendar-alt"></i></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>8</h3>
                                <p>Komplain Status Open</p>
                            </div>
                            <div class="icon"><i class="fas fa-folder-open"></i></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-secondary">
                            <div class="inner">
                                <h3>5</h3>
                                <p>Komplain On Progress</p>
                            </div>
                            <div class="icon"><i class="fas fa-spinner"></i></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>20</h3>
                                <p>Komplain Closed/Selesai</p>
                            </div>
                            <div class="icon"><i class="fas fa-check-circle"></i></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-teal">
                            <div class="inner">
                                <h3>100</h3>
                                <p>Member Aktif</p>
                            </div>
                            <div class="icon"><i class="fas fa-users"></i></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-purple">
                            <div class="inner">
                                <h3>7</h3>
                                <p>CS Aktif</p>
                            </div>
                            <div class="icon"><i class="fas fa-headset"></i></div>
                        </div>
                    </div>
                </div>

                <!-- 2. Grafik / Chart -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header"><h3 class="card-title">Grafik Komplain 7 Hari Terakhir</h3></div>
                            <div class="card-body">
                                <canvas id="chart-komplain-harian" height="120"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header"><h3 class="card-title">Grafik Komplain per Bulan</h3></div>
                            <div class="card-body">
                                <canvas id="chart-komplain-bulanan" height="120"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header"><h3 class="card-title">Grafik Rating CS</h3></div>
                            <div class="card-body">
                                <canvas id="chart-rating-cs" height="120"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header"><h3 class="card-title">Top 5 CS (Komplain Selesai)</h3></div>
                            <div class="card-body">
                                <ol>
                                    <li>CS A - 20 komplain</li>
                                    <li>CS B - 18 komplain</li>
                                    <li>CS C - 15 komplain</li>
                                    <li>CS D - 12 komplain</li>
                                    <li>CS E - 10 komplain</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Daftar Komplain Terbaru -->
                <div class="card mt-3">
                    <div class="card-header"><h3 class="card-title">Daftar Komplain Terbaru</h3></div>
                    <div class="card-body p-0">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Member</th>
                                    <th>CS</th>
                                    <th>Status</th>
                                    <th>Last Message</th>
                                    <th>Last Activity</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Member A</td>
                                    <td>CS A</td>
                                    <td><span class="badge badge-warning">Open</span></td>
                                    <td>Pesan terakhir...</td>
                                    <td>5 menit lalu</td>
                                    <td><a href="#" class="btn btn-sm btn-info">Lihat</a></td>
                                </tr>
                                <tr>
                                    <td>Member B</td>
                                    <td>CS B</td>
                                    <td><span class="badge badge-success">Closed</span></td>
                                    <td>Terima kasih</td>
                                    <td>10 menit lalu</td>
                                    <td><a href="#" class="btn btn-sm btn-info">Lihat</a></td>
                                </tr>
                                <tr>
                                    <td>Member C</td>
                                    <td>CS C</td>
                                    <td><span class="badge badge-primary">On Progress</span></td>
                                    <td>Sedang diproses</td>
                                    <td>1 menit lalu</td>
                                    <td><a href="#" class="btn btn-sm btn-info">Lihat</a></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 4. Daftar CS Online / Offline -->
                <div class="card mt-3">
                    <div class="card-header"><h3 class="card-title">Daftar CS Online / Offline</h3></div>
                    <div class="card-body p-0">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Nama CS</th>
                                    <th>Status</th>
                                    <th>Komplain Aktif</th>
                                    <th>Rating Rata-rata</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>CS A</td>
                                    <td><span class="badge badge-success">Online</span></td>
                                    <td>3</td>
                                    <td>4.8</td>
                                </tr>
                                <tr>
                                    <td>CS B</td>
                                    <td><span class="badge badge-secondary">Offline</span></td>
                                    <td>0</td>
                                    <td>4.5</td>
                                </tr>
                                <tr>
                                    <td>CS C</td>
                                    <td><span class="badge badge-success">Online</span></td>
                                    <td>2</td>
                                    <td>4.9</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 5. Notifikasi Sistem -->
                <div class="card mt-3">
                    <div class="card-header"><h3 class="card-title">Notifikasi Sistem</h3></div>
                    <div class="card-body">
                        <ul>
                            <li>Komplain baru dari Member D</li>
                            <li>Komplain #123 tidak ada respon &gt; 10 menit</li>
                            <li>Member E menutup komplain #124</li>
                            <li>CS B mendapatkan rating buruk</li>
                        </ul>
                    </div>
                </div>

                <!-- 6. Menu Akses Cepat (Quick Actions) -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="btn-group mb-2">
                            <a href="#" class="btn btn-primary">Tambah CS</a>
                            <a href="#" class="btn btn-info">Lihat Semua Komplain</a>
                            <a href="#" class="btn btn-success">Export Rekap</a>
                            <a href="#" class="btn btn-warning">Lihat Komplain Open</a>
                            <a href="#" class="btn btn-danger">Lihat Komplain Tidak Terjawab</a>
                        </div>
                    </div>
                </div>

                <!-- 7. Aktivitas Terakhir -->
                <div class="card mt-3">
                    <div class="card-header"><h3 class="card-title">Aktivitas Terakhir</h3></div>
                    <div class="card-body">
                        <ul>
                            <li>CS A menjawab komplain #122</li>
                            <li>Member B menutup komplain #119</li>
                            <li>Admin mengubah status komplain #120</li>
                            <li>CS C keluar sistem</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-layouts.app>
