<x-app-layout>
    <h2 class="page-title">RIWAYAT PENGAJUAN SK</h2>

    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3>Data Pengajuan Saya</h3>
        </div>

        @if($pengajuan->isEmpty())
            <div class="empty-state">
                <div class="empty-state-icon">📂</div>
                <p class="empty-state-text">Belum ada pengajuan.</p>
                <p class="empty-state-sub">Silakan input data pengajuan terlebih dahulu.</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table class="custom-table">
                    <thead><tr><th>NO</th><th>NAMA</th><th>ALAMAT</th><th>TANGGAL</th><th>STATUS</th><th style="text-align:center">AKSI</th></tr></thead>
                    <tbody>
                    @foreach($pengajuan as $row)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $row->nama }}</td>
                            <td>{{ $row->alamat }}</td>
                            <td>{{ $row->tanggal }}</td>
                            <td>
                                @if($row->status === 'pending')
                                    <span class="badge badge-pending">PENDING</span>
                                @elseif($row->status === 'diterima')
                                    <span class="badge badge-accepted">DITERIMA</span>
                                @else
                                    <span class="badge badge-rejected">DITOLAK</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                @if($row->status === 'pending')
                                    <form action="{{ route('petugas.pengajuan.destroy', $row) }}" method="POST" style="display:inline" onsubmit="return confirm('Hapus pengajuan ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                @else
                                    <span style="color: #9ca3af; font-size: 0.8rem;">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div style="margin-top: 1rem; text-align: right; font-size: 0.8rem; color: #6b7280;">Total {{ $pengajuan->count() }} pengajuan</div>
        @endif
    </div>
</x-app-layout>
