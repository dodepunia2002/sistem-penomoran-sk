<x-app-layout>
    <h2 class="page-title">MANAJEMEN USER</h2>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div style="background:#dcfce7; border:1px solid #86efac; color:#166534; padding:0.85rem 1.25rem; border-radius:0.5rem; margin-bottom:1.25rem; display:flex; align-items:center; gap:0.6rem;">
            ✅ {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="background:#fee2e2; border:1px solid #fca5a5; color:#991b1b; padding:0.85rem 1.25rem; border-radius:0.5rem; margin-bottom:1.25rem; display:flex; align-items:center; gap:0.6rem;">
            ❌ {{ session('error') }}
        </div>
    @endif

    {{-- Global validation errors --}}
    @if($errors->any())
        <div style="background:#fef9c3; border:1px solid #fde047; color:#713f12; padding:0.85rem 1.25rem; border-radius:0.5rem; margin-bottom:1.25rem;">
            <strong>⚠️ Periksa kembali form:</strong>
            <ul style="margin:0.4rem 0 0 1.2rem; font-size:0.85rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Add User Form -->
    <div class="card">
        <h3 style="margin-bottom: 1rem;">Tambah User Baru</h3>
        <form method="POST" action="{{ route('admin.users.store') }}" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-start;">
            @csrf
            <div class="form-group" style="flex: 1; min-width: 150px;">
                <label>Nama Lengkap <span style="color:#ef4444">*</span></label>
                <input type="text" name="name" class="form-input {{ $errors->has('name') ? 'is-invalid' : '' }}" required placeholder="Nama lengkap" value="{{ old('name') }}">
                @error('name') <small style="color:#ef4444">{{ $message }}</small> @enderror
            </div>
            <div class="form-group" style="flex: 1; min-width: 150px;">
                <label>Email <span style="color:#ef4444">*</span></label>
                <input type="email" name="email" class="form-input {{ $errors->has('email') ? 'is-invalid' : '' }}" required placeholder="email@dishub.go.id" value="{{ old('email') }}">
                @error('email') <small style="color:#ef4444">{{ $message }}</small> @enderror
            </div>
            <div class="form-group" style="flex: 1; min-width: 130px;">
                <label>Password <span style="color:#ef4444">*</span></label>
                <input type="password" name="password" class="form-input {{ $errors->has('password') ? 'is-invalid' : '' }}" required placeholder="Min 6 karakter">
                @error('password') <small style="color:#ef4444">{{ $message }}</small> @enderror
            </div>
            <div class="form-group" style="flex: 1; min-width: 130px;">
                <label>Konfirmasi Password <span style="color:#ef4444">*</span></label>
                <input type="password" name="password_confirmation" class="form-input" required placeholder="Ulangi password">
            </div>
            <div class="form-group" style="min-width: 120px;">
                <label>Role <span style="color:#ef4444">*</span></label>
                <select name="role" class="form-input" required>
                    <option value="petugas" {{ old('role') === 'petugas' ? 'selected' : '' }}>Petugas</option>
                    <option value="admin"   {{ old('role') === 'admin'   ? 'selected' : '' }}>Admin</option>
                </select>
            </div>
            <div style="display:flex; align-items:flex-end; padding-bottom:0.1rem;">
                <button type="submit" class="btn btn-primary" style="height: fit-content;">TAMBAH</button>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="card">
        <h3 style="margin-bottom: 1rem;">Daftar User ({{ $users->count() }})</h3>
        <div style="overflow-x: auto;">
            <table class="custom-table">
                <thead><tr><th>NO</th><th>NAMA</th><th>EMAIL</th><th>ROLE</th><th>DIBUAT</th><th style="text-align:center">AKSI</th></tr></thead>
                <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td><span class="badge badge-{{ $user->role }}">{{ strtoupper($user->role) }}</span></td>
                        <td>{{ $user->created_at->format('d M Y') }}</td>
                        <td style="text-align: center;">
                            @if($user->id !== auth()->id())
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display:inline" onsubmit="return confirm('Hapus user {{ addslashes($user->name) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            @else
                                <span style="color: #9ca3af; font-size: 0.8rem;">— Anda —</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>

