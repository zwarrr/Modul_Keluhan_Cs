<x-layouts.app>
    <x-slot name="header">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Edit Akun</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('dataakuns.index') }}">Data Akun</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>
        </div>
    </x-slot>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Form Edit Akun: {{ $user->name }}</h3>
        </div>
        <form action="{{ route('dataakuns.update', $user->id) }}?source={{ $user->table_source }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="source" value="{{ $user->table_source }}">
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

                <div class="form-group">
                    <label for="role">Role/Level <span class="text-danger">*</span></label>
                    <select name="role" id="role" class="form-control @error('role') is-invalid @enderror" required>
                        <option value="">-- Pilih Role --</option>
                        <option value="cs" {{ old('role', $user->role) == 'cs' ? 'selected' : '' }}>CS (Customer Service)</option>
                        <option value="member" {{ old('role', $user->role) == 'member' ? 'selected' : '' }}>Member</option>
                    </select>
                    @error('role')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="name">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                           value="{{ old('name', $user->name) }}" required>
                    @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group" id="emailFieldGroup">
                    <label for="email" id="emailLabel">Email</label>
                    <input type="text" name="email" id="email" class="form-control @error('email') is-invalid @enderror" 
                           value="{{ old('email', $user->role == 'member' ? ($user->member_id ?? '') : $user->email) }}">
                    @error('email')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                    <small class="form-text text-muted" id="emailHelp"></small>
                </div>

                <div class="form-group">
                    <label for="password">Password Baru</label>
                    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror">
                    @error('password')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                    <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah password</small>
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                </div>

                <div class="form-group">
                    <label for="phone_number">No. Telepon</label>
                    <input type="text" name="phone_number" id="phone_number" class="form-control @error('phone_number') is-invalid @enderror" 
                           value="{{ old('phone_number', $user->phone_number) }}">
                    @error('phone_number')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group" id="addressFieldGroup">
                    <label for="address">Alamat</label>
                    <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" rows="3">{{ old('address', $user->address) }}</textarea>
                    @error('address')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update
                </button>
                <a href="{{ route('dataakuns.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </form>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelect = document.getElementById('role');
        const emailFieldGroup = document.getElementById('emailFieldGroup');
        const emailLabel = document.getElementById('emailLabel');
        const emailInput = document.getElementById('email');
        const emailHelp = document.getElementById('emailHelp');
        const addressFieldGroup = document.getElementById('addressFieldGroup');

        function updateFieldsByRole() {
            const role = roleSelect.value;
            
            if (role === 'member') {
                // Show email field as Member ID (optional)
                emailFieldGroup.style.display = 'block';
                emailLabel.innerHTML = 'Member ID';
                emailInput.type = 'text';
                emailInput.placeholder = 'Contoh: 404040';
                emailInput.removeAttribute('required');
                emailHelp.textContent = 'Opsional - ID member';
                
                // Show address field for members
                addressFieldGroup.style.display = 'block';
            } else if (role === 'cs') {
                // Show email field as Email (required)
                emailFieldGroup.style.display = 'block';
                emailLabel.innerHTML = 'Email <span class="text-danger">*</span>';
                emailInput.type = 'email';
                emailInput.placeholder = '';
                emailInput.setAttribute('required', 'required');
                emailHelp.textContent = 'Wajib diisi untuk akun CS';
                
                // Hide address field for CS
                addressFieldGroup.style.display = 'none';
            } else {
                // Hide both fields when no role selected
                emailFieldGroup.style.display = 'none';
                addressFieldGroup.style.display = 'none';
            }
        }

        // Initial state based on current role
        updateFieldsByRole();

        // Listen to role changes
        roleSelect.addEventListener('change', updateFieldsByRole);
    });
    </script>
</x-layouts.app>
