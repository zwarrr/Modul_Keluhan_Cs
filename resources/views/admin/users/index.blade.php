<x-layouts.app>
    <x-slot name="header">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Data Akun</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Home</a></li>
                    <li class="breadcrumb-item active">Data Akun</li>
                </ol>
            </div>
        </div>
    </x-slot>

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">Daftar Akun Member & CS</h3>
                <a href="{{ route('dataakuns.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Akun
                </a>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th style="width: 10px">#</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role/Level</th>
                        <th>No. Telepon</th>
                        <th>Terdaftar</th>
                        <th style="width: 120px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $user['name'] }}</td>
                            <td>{{ $user['email'] }}</td>
                            <td>
                                @if($user['role'] === 'admin')
                                    <span class="badge badge-danger">Admin</span>
                                @elseif($user['role'] === 'cs')
                                    <span class="badge badge-info">CS</span>
                                @elseif($user['role'] === 'member')
                                    <span class="badge badge-success">Member</span>
                                @endif
                            </td>
                            <td>{{ $user['phone_number'] ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($user['created_at'])->format('d M Y') }}</td>
                            <td>
                                @if($user['role'] !== 'admin')
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('dataakuns.edit', $user['id']) }}?source={{ $user['table_source'] }}" class="btn btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('dataakuns.destroy', $user['id']) }}?source={{ $user['table_source'] }}" method="POST" 
                                              onsubmit="return confirm('Yakin ingin menghapus akun ini?')" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="badge badge-secondary">Tidak dapat diubah</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada data akun</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
