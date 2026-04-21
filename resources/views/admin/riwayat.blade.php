<x-app-layout>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h2 class="page-title" style="margin-bottom:0">RIWAYAT PENOMORAN SK</h2>
        <button class="btn btn-primary no-print" onclick="window.print()">🖨 CETAK LAPORAN</button>
    </div>

    <div class="card">
        <form method="GET" class="no-print" style="margin-bottom: 1.5rem; display: flex; gap: 1rem; align-items: center;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, alamat, atau nomor SK..." class="form-input" style="flex: 1;">
            <button type="submit" class="btn btn-primary btn-sm">Cari</button>
            @if(request('search'))
                <a href="{{ route('admin.riwayat') }}" class="btn btn-cancel btn-sm">Reset</a>
            @endif
        </form>

        @if($riwayat->isEmpty())
            <div class="empty-state">
                <div class="empty-state-icon">📂</div>
                <p class="empty-state-text">{{ request('search') ? 'Tidak ada data yang cocok.' : 'Belum ada riwayat.' }}</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table class="custom-table">
                    <thead><tr><th>NO</th><th>ID</th><th>NAMA</th><th>ALAMAT</th><th>TANGGAL</th><th>NOMOR SK</th><th class="no-print" style="text-align:center">AKSI</th></tr></thead>
                    <tbody>
                    @foreach($riwayat as $row)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>#{{ str_pad($row->id, 5, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $row->nama }}</td>
                            <td>{{ $row->alamat }}</td>
                            <td>{{ $row->tanggal }}</td>
                            <td><span class="sk-badge">{{ $row->nomor_sk }}</span></td>
                            <td class="no-print" style="text-align: center;">
                                <form action="{{ route('admin.riwayat.destroy', $row) }}" method="POST" style="display:inline" onsubmit="return confirm('Hapus data ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div style="margin-top: 1rem;">{{ $riwayat->links() }}</div>
        @endif
    </div>
</x-app-layout>
