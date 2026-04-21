<x-app-layout>
    <h2 class="page-title">SISTEM PENOMORAN SK</h2>

    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3>Daftar Pengajuan Pending</h3>
            <span class="badge badge-pending">{{ $pengajuan->where('status', 'pending')->count() }} pengajuan</span>
        </div>

        @php $pending = $pengajuan->where('status', 'pending'); @endphp

        @if($pending->isEmpty())
            <div class="empty-state">
                <div class="empty-state-icon">📋</div>
                <p class="empty-state-text">Belum ada pengajuan baru.</p>
                <p class="empty-state-sub">Data pengajuan dari Petugas akan muncul di sini.</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table class="custom-table">
                    <thead><tr><th>NO</th><th>NAMA</th><th>ALAMAT</th><th>TANGGAL</th><th>PETUGAS</th><th style="text-align:center">KONFIRMASI</th></tr></thead>
                    <tbody>
                    @foreach($pending as $idx => $row)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $row->nama }}</td>
                            <td>{{ $row->alamat }}</td>
                            <td>{{ $row->tanggal }}</td>
                            <td><span class="badge badge-petugas">{{ $row->submitter->name ?? '-' }}</span></td>
                            <td style="text-align: center;">
                                <form action="{{ route('admin.pengajuan.tolak', $row) }}" method="POST" style="display:inline">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Tolak pengajuan ini?')">TOLAK</button>
                                </form>
                                <form action="{{ route('admin.pengajuan.terima', $row) }}" method="POST" style="display:inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Terima pengajuan dari {{ addslashes($row->nama) }}?\nNomor SK akan digenerate otomatis.')">TERIMA</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-app-layout>
