<x-layouts.app>
    <x-slot:header>

    </x-slot:header>

    <section class="content">
        @php
            $listCs = [
                ['id' => 1, 'name' => 'CS Siti'],
                ['id' => 2, 'name' => 'CS Budi'],
                ['id' => 3, 'name' => 'CS Rina'],
            ];
            $listMember = [
                ['id' => 10, 'name' => 'Member Ali'],
                ['id' => 11, 'name' => 'Member Dinda'],
                ['id' => 12, 'name' => 'Member Joko'],
            ];
        @endphp
        <div class="row">
            <div class="col-md-3">

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Filter</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="">
                            <div class="mb-2">
                                <label for="filter_cs" class="form-label mb-0">CS</label>
                                <select name="cs" id="filter_cs" class="form-control form-control-sm">
                                    <option value="">Semua CS</option>
                                    @foreach($listCs as $cs)
                                        <option value="{{ $cs['id'] }}" {{ request('cs') == $cs['id'] ? 'selected' : '' }}>{{ $cs['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-2">
                                <label for="filter_member" class="form-label mb-0">Member</label>
                                <select name="member" id="filter_member" class="form-control form-control-sm">
                                    <option value="">Semua Member</option>
                                    @foreach($listMember as $member)
                                        <option value="{{ $member['id'] }}" {{ request('member') == $member['id'] ? 'selected' : '' }}>{{ $member['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-2">
                                <label for="filter_status" class="form-label mb-0">Status</label>
                                <select name="status" id="filter_status" class="form-control form-control-sm">
                                    <option value="">Semua Status</option>
                                    <option value="Open" {{ request('status') == 'Open' ? 'selected' : '' }}>Open</option>
                                    <option value="On Progress" {{ request('status') == 'On Progress' ? 'selected' : '' }}>On Progress</option>
                                    <option value="Closed" {{ request('status') == 'Closed' ? 'selected' : '' }}>Closed</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label for="filter_periode" class="form-label mb-0">Periode</label>
                                <select name="periode" id="filter_periode" class="form-control form-control-sm">
                                    <option value="">Semua</option>
                                    <option value="minggu" {{ request('periode') == 'minggu' ? 'selected' : '' }}>Minggu ini</option>
                                    <option value="bulan" {{ request('periode') == 'bulan' ? 'selected' : '' }}>Bulan ini</option>
                                    <option value="tahun" {{ request('periode') == 'tahun' ? 'selected' : '' }}>Tahun ini</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label for="filter_sort" class="form-label mb-0">Urutkan</label>
                                <select name="sort" id="filter_sort" class="form-control form-control-sm">
                                    <option value="desc" {{ request('sort', 'desc') == 'desc' ? 'selected' : '' }}>Terbaru ke Terlama</option>
                                    <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Terlama ke Terbaru</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
                        </form>
                    </div>
                </div>
            </div>
            <!-- /.col -->
            <div class="col-md-9">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Pesan Terbaru</h3>

                        <div class="card-tools">
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control" placeholder="Search Mail">
                                <div class="input-group-append">
                                    <div class="btn btn-primary">
                                        <i class="fas fa-search"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-tools -->
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body p-0">
                        <div class="mailbox-controls">
                            <!-- Check all button -->
                            <button type="button" class="btn btn-default btn-sm checkbox-toggle"><i
                                    class="far fa-square"></i>
                            </button>
                            <div class="btn-group">
                                <button type="button" class="btn btn-default btn-sm"><i
                                        class="far fa-trash-alt"></i></button>
                                <button type="button" class="btn btn-default btn-sm"><i
                                        class="fas fa-reply"></i></button>
                                <button type="button" class="btn btn-default btn-sm"><i
                                        class="fas fa-share"></i></button>
                            </div>
                            <!-- /.btn-group -->
                            <button type="button" class="btn btn-default btn-sm"><i
                                    class="fas fa-sync-alt"></i></button>
                            <div class="float-right">
                                1-50/200
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default btn-sm"><i
                                            class="fas fa-chevron-left"></i></button>
                                    <button type="button" class="btn btn-default btn-sm"><i
                                            class="fas fa-chevron-right"></i></button>
                                </div>
                                <!-- /.btn-group -->
                            </div>
                            <!-- /.float-right -->
                        </div>
                        <!-- Chat List ala Telegram/WA -->
                        <div class="list-group list-group-flush">
                            <!-- Contoh chat item, nanti bisa di-loop -->
                            <a href="{{ route('admin.sesi-chat.detail', ['id' => 1]) }}"
                                class="list-group-item list-group-item-action d-flex align-items-center py-3">
                                <div class="flex-shrink-0 me-3">
                                    <img src="https://ui-avatars.com/api/?name=Alexander+Pierce" alt="Avatar"
                                        class="rounded-circle" style="width:48px;height:48px;object-fit:cover;">
                                </div>
                                <div class="flex-grow-1 ms-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">Alexander Pierce <span
                                                class="badge badge-success ms-2">Online</span></h6>
                                        <small class="text-muted">5 menit lalu</small>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">Trying to find a solution to this problem...</span>
                                        <span class="badge badge-warning">Open</span>
                                    </div>
                                </div>
                            </a>
                            <a href="{{ route('admin.sesi-chat.detail', ['id' => 2]) }}"
                                class="list-group-item list-group-item-action d-flex align-items-center py-3 bg-light">
                                <div class="flex-shrink-0 me-3">
                                    <img src="https://ui-avatars.com/api/?name=Sarah+Connor" alt="Avatar"
                                        class="rounded-circle" style="width:48px;height:48px;object-fit:cover;">
                                </div>
                                <div class="flex-grow-1 ms-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">Sarah Connor <span
                                                class="badge badge-secondary ms-2">Offline</span></h6>
                                        <small class="text-muted">10 menit lalu</small>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">Terima kasih atas bantuannya!</span>
                                        <span class="badge badge-success">Closed</span>
                                    </div>
                                </div>
                            </a>
                            <a href="{{ route('admin.sesi-chat.detail', ['id' => 3]) }}"
                                class="list-group-item list-group-item-action d-flex align-items-center py-3">
                                <div class="flex-shrink-0 me-3">
                                    <img src="https://ui-avatars.com/api/?name=John+Doe" alt="Avatar"
                                        class="rounded-circle" style="width:48px;height:48px;object-fit:cover;">
                                </div>
                                <div class="flex-grow-1 ms-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">John Doe <span
                                                class="badge badge-success ms-2">Online</span></h6>
                                        <small class="text-muted">1 menit lalu</small>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">Pesan terakhir dari John...</span>
                                        <span class="badge badge-primary">On Progress</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <!-- /.mail-box-messages -->
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer p-0">
                        <div class="mailbox-controls">
                            <!-- Check all button -->
                            <button type="button" class="btn btn-default btn-sm checkbox-toggle"><i
                                    class="far fa-square"></i>
                            </button>
                            <div class="btn-group">
                                <button type="button" class="btn btn-default btn-sm"><i
                                        class="far fa-trash-alt"></i></button>
                                <button type="button" class="btn btn-default btn-sm"><i
                                        class="fas fa-reply"></i></button>
                                <button type="button" class="btn btn-default btn-sm"><i
                                        class="fas fa-share"></i></button>
                            </div>
                            <!-- /.btn-group -->
                            <button type="button" class="btn btn-default btn-sm"><i
                                    class="fas fa-sync-alt"></i></button>
                            <div class="float-right">
                                1-50/200
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default btn-sm"><i
                                            class="fas fa-chevron-left"></i></button>
                                    <button type="button" class="btn btn-default btn-sm"><i
                                            class="fas fa-chevron-right"></i></button>
                                </div>
                                <!-- /.btn-group -->
                            </div>
                            <!-- /.float-right -->
                        </div>
                    </div>
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
    </section>
</x-layouts.app>
