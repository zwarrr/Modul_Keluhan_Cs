<x-layouts.app>
    <x-slot name="header">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Tambah Akun</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('dataakuns.index') }}">Data Akun</a></li>
                    <li class="breadcrumb-item active">Tambah</li>
                </ol>
            </div>
        </div>
    </x-slot>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Form Tambah Akun Member atau CS</h3>
        </div>
        <form action="{{ route('dataakuns.store') }}" method="POST">
            @csrf
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
                        <option value="member" {{ old('role') === 'member' ? 'selected' : '' }}>Member</option>
                        <option value="cs" {{ old('role') === 'cs' ? 'selected' : '' }}>CS</option>
                    </select>
                    @error('role')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                    <small class="form-text text-muted">Admin tidak dapat ditambahkan (hanya ada 1 akun admin)</small>
                </div>

                <div class="form-group">
                    <label for="name">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                           value="{{ old('name') }}" required>
                    @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group" id="emailFieldGroup" style="display: none;">
                    <label for="email" id="emailLabel">Email <span class="text-danger" id="emailRequired">*</span></label>
                    <input type="text" name="email" id="email" class="form-control @error('email') is-invalid @enderror" 
                           value="{{ old('email') }}">
                    @error('email')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                    <small class="form-text text-muted" id="emailHelp"></small>
                </div>

                <div class="form-group">
                    <label for="password">Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required>
                    @error('password')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Password <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="phone_number">No. Telepon</label>
                    <input type="text" name="phone_number" id="phone_number" class="form-control @error('phone_number') is-invalid @enderror" 
                           value="{{ old('phone_number') }}">
                    @error('phone_number')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group" id="addressFieldGroup" style="display: none;">
                    <label for="address">Alamat</label>
                    <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" rows="3">{{ old('address') }}</textarea>
                    @error('address')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan
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
        const emailRequired = document.getElementById('emailRequired');
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
                emailRequired.style.display = 'none';
                emailInput.removeAttribute('required');
                emailHelp.textContent = 'Opsional - ID member akan otomatis digenerate jika tidak diisi';
                
                // Show address field for members
                addressFieldGroup.style.display = 'block';
            } else if (role === 'cs') {
                // Show email field as Email (required)
                emailFieldGroup.style.display = 'block';
                emailLabel.innerHTML = 'Email <span class="text-danger" id="emailRequired">*</span>';
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

        // Initial state based on old input (if validation error)
        updateFieldsByRole();

        // Listen to role changes
        roleSelect.addEventListener('change', updateFieldsByRole);
    });
    </script>
</x-layouts.app>
