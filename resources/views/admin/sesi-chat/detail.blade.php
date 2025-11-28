<x-layouts.app>
    <x-slot:header>
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Detail Sesi Chat #{{ $chatSesi['id'] }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/admin/sesi-chat">Sesi Chat</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </div>
        </div>
    </x-slot:header>

    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-10 col-xl-8">
                    <!-- Info Sesi Card -->
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Info Sesi</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-2 mb-md-0">
                                    <div class="info-box bg-light">
                                        <span class="info-box-icon bg-primary"><i class="fas fa-user"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Member</span>
                                            <span class="info-box-number">{{ $chatSesi['member'] }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-box bg-light">
                                        <span class="info-box-icon bg-info"><i class="fas fa-headset"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Customer Service</span>
                                            <span class="info-box-number">{{ $chatSesi['cs'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-md-6 mb-2 mb-md-0">
                                    <strong>Status:</strong>
                                    <span class="badge badge-{{ $chatSesi['status'] == 'Open' ? 'warning' : ($chatSesi['status'] == 'Closed' ? 'success' : 'primary') }} ml-2">
                                        {{ $chatSesi['status'] }}
                                    </span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Last Activity:</strong>
                                    <span class="text-muted ml-2">{{ $chatSesi['last_activity'] }}</span>
                                </div>
                            </div>
                            
                            <div class="mt-3 p-3 bg-light rounded">
                                <strong>Last Message:</strong>
                                <p class="mb-0 mt-1">{{ $chatSesi['last_message'] }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Percakapan Card -->
                    <div class="card card-outline card-info direct-chat direct-chat-info">
                        <div class="card-header">
                            <h3 class="card-title">Percakapan</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="direct-chat-messages" style="height: 400px; overflow-y: auto;">
                                @foreach($pesans as $pesan)
                                    <div class="direct-chat-msg {{ $pesan['role'] === 'cs' ? 'right' : '' }} mb-3">
                                        <div class="direct-chat-infos clearfix">
                                            <span class="direct-chat-name {{ $pesan['role'] === 'cs' ? 'float-right' : 'float-left' }}">
                                                {{ $pesan['sender'] }}
                                            </span>
                                            <span class="direct-chat-timestamp {{ $pesan['role'] === 'cs' ? 'float-left' : 'float-right' }}">
                                                {{ $pesan['time'] }}
                                            </span>
                                        </div>
                                        <img class="direct-chat-img" src="https://ui-avatars.com/api/?name={{ urlencode($pesan['sender']) }}&size=40&background={{ $pesan['role'] === 'cs' ? '007bff' : '6c757d' }}&color=fff" alt="{{ $pesan['sender'] }}">
                                        <div class="direct-chat-text {{ $pesan['role'] === 'cs' ? 'bg-primary' : 'bg-light' }}">
                                            {{ $pesan['message'] }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <!-- Input Pesan -->
                        <div class="card-footer">
                            <form action="#" method="post">
                                <div class="input-group">
                                    <input type="text" name="message" placeholder="Ketik Pesan ..." class="form-control">
                                    <span class="input-group-append">
                                        <button type="submit" class="btn btn-primary">Kirim</button>
                                    </span>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body text-center">
                                    <button type="button" class="btn btn-success mr-2">
                                        <i class="fas fa-check mr-1"></i> Tandai Selesai
                                    </button>
                                    <button type="button" class="btn btn-warning mr-2">
                                        <i class="fas fa-sync-alt mr-1"></i> Refresh
                                    </button>
                                    <button type="button" class="btn btn-danger">
                                        <i class="fas fa-times mr-1"></i> Tutup Sesi
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>