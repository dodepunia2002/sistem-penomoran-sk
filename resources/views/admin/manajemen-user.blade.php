<x-app-layout>
    <h2 class="page-title">MANAJEMEN USER</h2>

    <!-- Add User Form -->
    <div class="card">
        <h3 style="margin-bottom: 1rem;">Tambah User Baru</h3>
        <form method="POST" action="{{ route('admin.users.store') }}" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            @csrf
            <div class="form-group" style="flex: 1; min-width: 150px;">
                <label>Nama Lengkap</label>
                <input type="text" name="name" class="form-input" required placeholder="Nama lengkap">
            </div>
            <div class="form-group" style="flex: 1; min-width: 150px;">
                <label>Email</label>
                <input type="email" name="email" class="form-input" required placeholder="email@dishub.go.id">
            </div>
            <div class="form-group" style="flex: 1; min-width: 120px;">
                <label>Password</label>
                <input type="password" name="password" class="form-input" required placeholder="Min 6 karakter">
            </div>
            <div class="form-group" style="min-width: 120px;">
                <label>Role</label>
                <select name="role" class="form-input" required>
                    <option value="petugas">Petugas</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" style="height: fit-content;">TAMBAH</button>
        </form>
        @error('email') <p style="color: #ef4444; font-size: 0.85rem; margin-top: 0.5rem;">{{ $message }}</p> @enderror
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
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display:inline" onsubmit="return confirm('Hapus user ini?')">
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
