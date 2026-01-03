<x-layouts.app>
    <x-slot name="header">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Profil Pengguna</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/cs/chat">Home</a></li>
                    <li class="breadcrumb-item active">Profil</li>
                </ol>
            </div>
        </div>
    </x-slot>

    <div class="row">
        <div class="col-md-7 ml-3">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <!-- Form Ubah Profil -->
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-user-edit"></i> Ubah Profil</h3>
                </div>
                <form action="{{ route('cs.profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="form-group row">
                            <label for="role" class="col-sm-3 col-form-label">Role</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" value="{{ strtoupper($user->role) }}" disabled>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="name" class="col-sm-3 col-form-label">Nama Lengkap</label>
                            <div class="col-sm-9">
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="phone_number" class="col-sm-3 col-form-label">No. HP/WA</label>
                            <div class="col-sm-9">
                                <input type="text" name="phone_number" id="phone_number" 
                                       class="form-control @error('phone_number') is-invalid @enderror" 
                                       value="{{ old('phone_number', $user->phone_number) }}"
                                       placeholder="Contoh: 628123456789">
                                @error('phone_number')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-sm-3 col-form-label">Email</label>
                            <div class="col-sm-9">
                                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" 
                                       value="{{ old('email', $user->email) }}">
                                @error('email')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="form-text text-muted">Jika email diubah, status verifikasi akan direset.</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-right">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            <!-- Form Ubah Password -->
            <div class="card">
                <div class="card-header bg-secondary">
                    <h3 class="card-title"><i class="fas fa-lock"></i> Ubah Password</h3>
                </div>
                <form action="{{ route('cs.profile.updatePassword') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        @error('current_password')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror

                        <div class="form-group row">
                            <label for="current_password" class="col-sm-4 col-form-label">Password Saat Ini</label>
                            <div class="col-sm-8">
                                <input type="password" name="current_password" id="current_password" 
                                       class="form-control @error('current_password') is-invalid @enderror" required>
                                @error('current_password')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-sm-4 col-form-label">Password Baru</label>
                            <div class="col-sm-8">
                                <input type="password" name="password" id="password" 
                                       class="form-control @error('password') is-invalid @enderror" required>
                                @error('password')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password_confirmation" class="col-sm-4 col-form-label">Konfirmasi Password</label>
                            <div class="col-sm-8">
                                <input type="password" name="password_confirmation" id="password_confirmation" 
                                       class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-right">
                        <button type="submit" class="btn btn-secondary">
                            <i class="fas fa-key"></i> Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
