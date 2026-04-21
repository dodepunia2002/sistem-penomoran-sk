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
                                    <button type="button"
                                            class="btn btn-primary btn-sm detail-btn"
                                            style="margin-right: 0.25rem;"
                                            data-status="{{ $row->status }}"
                                            data-nama="{{ e($row->nama) }}"
                                            data-nomor="{{ $row->riwayat->nomor_sk ?? '—' }}"
                                            data-submitted="{{ $row->created_at->format('d M Y H:i') }}"
                                            data-approved="{{ $row->riwayat ? $row->riwayat->created_at->format('d M Y H:i') : '—' }}"
                                            data-processor="{{ $row->riwayat->processor->name ?? 'Admin' }}">
                                        🔍 Detail
                                    </button>
                                    @if($row->status === 'pending')
                                        <form action="{{ route('petugas.pengajuan.destroy', $row) }}"
                                              method="POST"
                                              style="display:inline"
                                              onsubmit="return confirm('Hapus pengajuan ini?\nTindakan ini tidak dapat dibatalkan.')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">🗑 Hapus</button>
                                        </form>
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

    {{-- Detail Modal --}}
    <div id="detailModal" class="modal-overlay no-print">
        <div class="modal-content" style="max-width: 500px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; border-bottom:1px solid #eee; padding-bottom:1rem;">
                <h3 style="margin:0; color:#1e293b;">📄 Detail Lifecycle Pengajuan</h3>
                <button type="button" onclick="closeDetailModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:#94a3b8;">&times;</button>
            </div>
            
            <div id="detailContent" style="font-size: 0.95rem;">
                <div style="background:#f8fafc; padding:1rem; border-radius:0.5rem; margin-bottom:1.5rem;">
                    <div style="margin-bottom:0.5rem;"><strong>Nomor SK:</strong> <span id="det_nomor" class="sk-badge"></span></div>
                    <div><strong>Nama:</strong> <span id="det_nama"></span></div>
                </div>

                <div style="display:flex; flex-direction:column; gap:1rem; position:relative; padding-left:1.5rem;">
                    <div style="position:absolute; left:4px; top:10px; bottom:10px; width:2px; background:#e2e8f0;"></div>
                    
                    <div style="position:relative;">
                        <div style="position:absolute; left:-22px; top:4px; width:10px; height:10px; border-radius:50%; background:#3b82f6; border:2px solid #fff;"></div>
                        <div style="font-weight:600; font-size:0.85rem; color:#64748b; text-transform:uppercase;">Diajukan (Anda)</div>
                        <div id="det_submitted" style="color:#1e293b;"></div>
                    </div>

                    <div id="approvedStep" style="position:relative;">
                        <div style="position:absolute; left:-22px; top:4px; width:10px; height:10px; border-radius:50%; background:#22c55e; border:2px solid #fff;"></div>
                        <div id="statusTitle" style="font-weight:600; font-size:0.85rem; color:#64748b; text-transform:uppercase;">Terbit / Disetujui</div>
                        <div id="det_approved" style="color:#1e293b;"></div>
                        <div style="font-size:0.85rem; color:#22c55e; margin-top:0.25rem;">Diproses oleh: <span id="det_processor" style="font-weight:600;"></span></div>
                    </div>
                </div>

                <div style="margin-top:1.5rem; padding-top:1rem; border-top:1px solid #eee; text-align:center;">
                    <button type="button" class="btn btn-primary" onclick="closeDetailModal()">TUTUP</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openDetailModal(btn) {
            const status = btn.dataset.status;
            document.getElementById('det_nomor').textContent = btn.dataset.nomor;
            document.getElementById('det_nama').textContent  = btn.dataset.nama;
            document.getElementById('det_submitted').textContent = btn.dataset.submitted;
            
            if (status === 'pending') {
                document.getElementById('approvedStep').style.opacity = '0.3';
                document.getElementById('det_approved').textContent = 'Sedang dalam antrian...';
                document.getElementById('det_processor').textContent = '—';
                document.getElementById('statusTitle').textContent = 'Menunggu Persetujuan';
            } else {
                document.getElementById('approvedStep').style.opacity = '1';
                document.getElementById('det_approved').textContent = btn.dataset.approved;
                document.getElementById('det_processor').textContent = btn.dataset.processor;
                document.getElementById('statusTitle').textContent = 'Terbit / Disetujui';
            }

            document.getElementById('detailModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeDetailModal() {
            document.getElementById('detailModal').classList.remove('active');
            document.body.style.overflow = '';
        }

        document.querySelectorAll('.detail-btn').forEach(function(btn) {
            btn.addEventListener('click', function() { openDetailModal(this); });
        });

        document.getElementById('detailModal').addEventListener('click', function(e) {
            if (e.target === this) closeDetailModal();
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeDetailModal();
        });
    </script>
</x-app-layout>
