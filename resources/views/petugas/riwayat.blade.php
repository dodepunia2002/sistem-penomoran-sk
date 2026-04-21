<x-app-layout>
    <h2 class="page-title">RIWAYAT PENGAJUAN SK</h2>

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

    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3>Data Pengajuan Saya</h3>
            <a href="{{ route('petugas.input-data') }}" class="btn btn-primary btn-sm">
                + Pengajuan Baru
            </a>
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
                    <thead>
                        <tr>
                            <th>NO</th>
                            <th>NAMA</th>
                            <th>ALAMAT</th>
                            <th>TANGGAL</th>
                            <th>STATUS</th>
                            <th>NOMOR SK</th>
                            <th style="text-align:center">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($pengajuan as $row)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $row->nama }}</td>
                            <td>{{ $row->alamat }}</td>
                            <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                            <td>
                                @if($row->status === 'pending')
                                    <span class="badge badge-pending">⏳ PENDING</span>
                                @elseif($row->status === 'diterima')
                                    <span class="badge badge-accepted">✅ DITERIMA</span>
                                @else
                                    <span class="badge badge-rejected">❌ DITOLAK</span>
                                @endif
                            </td>
                            <td>
                                @if($row->status === 'diterima' && $row->riwayat)
                                    <span class="sk-badge">{{ $row->riwayat->nomor_sk }}</span>
                                @elseif($row->status === 'pending')
                                    <span style="color:#9ca3af; font-size:0.8rem;">Menunggu...</span>
                                @else
                                    <span style="color:#d1d5db; font-size:0.8rem;">—</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                @if($row->status === 'pending')
                                    <form action="{{ route('petugas.pengajuan.destroy', $row) }}"
                                          method="POST"
                                          style="display:inline"
                                          onsubmit="return confirm('Hapus pengajuan ini?\nTindakan ini tidak dapat dibatalkan.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">🗑 Hapus</button>
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
            <div style="margin-top: 1rem; text-align: right; font-size: 0.8rem; color: #6b7280;">
                Total {{ $pengajuan->count() }} pengajuan
            </div>
        @endif
    </div>
</x-app-layout>
