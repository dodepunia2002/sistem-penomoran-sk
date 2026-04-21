<x-app-layout>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h2 class="page-title" style="margin-bottom:0">RIWAYAT PENOMORAN SK</h2>
        <a href="{{ route('admin.riwayat.cetak', request()->all()) }}" target="_blank" class="btn btn-primary no-print">
            📊 CETAK LAPORAN
        </a>
    </div>

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
        {{-- Filter & Search Bar --}}
        <form method="GET" class="no-print" style="margin-bottom: 1.5rem; display: flex; flex-wrap: wrap; gap: 1rem; align-items: center;">
            <div style="flex: 1; min-width: 250px; display: flex; gap: 0.5rem;">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari nama, alamat, atau nomor SK..."
                       class="form-input" style="flex: 1;">
            </div>

            <div style="display: flex; gap: 0.5rem; align-items: center;">
                <select name="month" class="form-input" style="padding-right: 2rem;">
                    <option value="">Semua Bulan</option>
                    @for($m=1; $m<=12; $m++)
                        <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>

                <select name="year" class="form-input">
                    <option value="">Semua Tahun</option>
                    @php $currentYear = date('Y'); @endphp
                    @for($y=$currentYear; $y>=$currentYear-5; $y--)
                        <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>

                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                @if(request()->anyFilled(['search', 'month', 'year']))
                    <a href="{{ route('admin.riwayat') }}" class="btn btn-cancel btn-sm">Reset</a>
                @endif
            </div>
        </form>

        @if($riwayat->isEmpty())
            <div class="empty-state">
                <div class="empty-state-icon">📂</div>
                <p class="empty-state-text">{{ request('search') ? 'Tidak ada data yang cocok.' : 'Belum ada riwayat penomoran SK.' }}</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>NO</th>
                            <th>ID</th>
                            <th>NAMA</th>
                            <th>ALAMAT</th>
                            <th>TANGGAL</th>
                            <th>NOMOR SK</th>
                            <th class="no-print" style="text-align:center; min-width:120px;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($riwayat as $row)
                        <tr>
                            <td>{{ $loop->iteration + ($riwayat->currentPage() - 1) * $riwayat->perPage() }}</td>
                            <td>#{{ str_pad($row->id, 5, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $row->nama }}</td>
                            <td>{{ $row->alamat }}</td>
                            <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                            <td><span class="sk-badge">{{ $row->nomor_sk }}</span></td>
                            <td class="no-print" style="text-align: center;">
                                {{-- Tombol Detail --}}
                                <button type="button"
                                        class="btn btn-primary btn-sm detail-btn"
                                        style="margin-right:0.25rem"
                                        data-nama="{{ e($row->nama) }}"
                                        data-alamat="{{ e($row->alamat) }}"
                                        data-tanggal="{{ $row->tanggal->format('d M Y') }}"
                                        data-nomor-sk="{{ e($row->nomor_sk) }}"
                                        data-submitted="{{ $row->pengajuan->created_at->format('d M Y H:i') }}"
                                        data-approved="{{ $row->created_at->format('d M Y H:i') }}"
                                        data-processor="{{ $row->processor->name ?? 'Admin' }}"
                                        data-diff="{{ $row->created_at->diffInDays($row->pengajuan->created_at) }}">
                                    🔍 Detail
                                </button>
                                </button>
                                {{-- Tombol Edit --}}
                                <button type="button"
                                        class="btn btn-warning btn-sm edit-btn"
                                        style="margin-right:0.25rem"
                                        data-id="{{ $row->id }}"
                                        data-nama="{{ e($row->nama) }}"
                                        data-alamat="{{ e($row->alamat) }}"
                                        data-tanggal="{{ $row->tanggal->format('Y-m-d') }}"
                                        data-nomor-sk="{{ e($row->nomor_sk) }}">
                                    ✏️ Edit
                                </button>
                                {{-- Tombol Hapus --}}
                                <form action="{{ route('admin.riwayat.destroy', $row) }}" method="POST"
                                      style="display:inline"
                                      onsubmit="return confirm('Hapus data SK {{ addslashes($row->nomor_sk) }}?\nTindakan ini tidak dapat dibatalkan.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">🗑 Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div style="margin-top: 1rem; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:0.5rem;">
                <span style="font-size:0.8rem; color:#6b7280;">
                    Menampilkan {{ $riwayat->firstItem() }}–{{ $riwayat->lastItem() }} dari {{ $riwayat->total() }} data
                </span>
                {{ $riwayat->links() }}
            </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════
         MODAL EDIT RIWAYAT SK
    ═══════════════════════════════════════════════════════════ --}}
    <div id="editModal"
         style="display:none; position:fixed; inset:0; z-index:9999;
                background:rgba(0,0,0,0.5); backdrop-filter:blur(4px);
                align-items:center; justify-content:center;">
        <div style="background:#fff; border-radius:1rem; padding:2rem; width:100%; max-width:540px;
                    box-shadow:0 25px 60px rgba(0,0,0,0.25); position:relative; margin:1rem;">

            {{-- Header --}}
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
                <h3 style="margin:0; font-size:1.1rem; font-weight:700; color:#1e293b;">
                    ✏️ Edit Data Riwayat SK
                </h3>
                <button onclick="closeEditModal()"
                        style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:#9ca3af; line-height:1;">&times;</button>
            </div>

            <form id="editForm" method="POST" action="">
                @csrf
                @method('PUT')

                <div class="form-group" style="margin-bottom:1rem;">
                    <label class="form-label">Nama <span style="color:#ef4444">*</span></label>
                    <input type="text" name="nama" id="edit_nama" class="form-input"
                           required placeholder="Nama lokasi / instansi">
                    @error('nama') <small style="color:#ef4444">{{ $message }}</small> @enderror
                </div>

                <div class="form-group" style="margin-bottom:1rem;">
                    <label class="form-label">Alamat <span style="color:#ef4444">*</span></label>
                    <input type="text" name="alamat" id="edit_alamat" class="form-input"
                           required placeholder="Alamat lengkap">
                    @error('alamat') <small style="color:#ef4444">{{ $message }}</small> @enderror
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem;">
                    <div class="form-group">
                        <label class="form-label">Tanggal <span style="color:#ef4444">*</span></label>
                        <input type="date" name="tanggal" id="edit_tanggal" class="form-input" required>
                        @error('tanggal') <small style="color:#ef4444">{{ $message }}</small> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nomor SK <span style="color:#ef4444">*</span></label>
                        <input type="text" name="nomor_sk" id="edit_nomor_sk" class="form-input"
                               required placeholder="SK/MM/XXXX/YYYY">
                        @error('nomor_sk') <small style="color:#ef4444">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div style="display:flex; justify-content:flex-end; gap:0.75rem; padding-top:1rem; border-top:1px solid #e5e7eb;">
                    <button type="button" onclick="closeEditModal()" class="btn btn-cancel">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        💾 Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .btn-warning {
            background: #f59e0b;
            color: #fff;
            border: none;
            padding: 0.35rem 0.75rem;
            border-radius: 0.375rem;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-warning:hover { background: #d97706; }

        #editModal.active { display: flex !important; }

        @media print {
            #editModal { display: none !important; }
        }
    </style>

    <script>
        const BASE_URL = '{{ url("/admin/riwayat") }}';

        function openEditModal(btn) {
            document.getElementById('edit_nama').value     = btn.dataset.nama;
            document.getElementById('edit_alamat').value   = btn.dataset.alamat;
            document.getElementById('edit_tanggal').value  = btn.dataset.tanggal;
            document.getElementById('edit_nomor_sk').value = btn.dataset.nomorSk;
            document.getElementById('editForm').action     = BASE_URL + '/' + btn.dataset.id;
            document.getElementById('editModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.remove('active');
            document.body.style.overflow = '';
        }

        function openDetailModal(btn) {
            document.getElementById('det_nomor_sk').textContent = btn.dataset.nomorSk;
            document.getElementById('det_nama').textContent     = btn.dataset.nama;
            document.getElementById('det_alamat').textContent   = btn.dataset.alamat;
            document.getElementById('det_submitted').textContent = btn.dataset.submitted;
            document.getElementById('det_approved').textContent  = btn.dataset.approved;
            document.getElementById('det_processor').textContent = btn.dataset.processor;
            document.getElementById('detailModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeDetailModal() {
            document.getElementById('detailModal').classList.remove('active');
            document.body.style.overflow = '';
        }

        // Attach click handlers to all edit buttons
        document.querySelectorAll('.edit-btn').forEach(function(btn) {
            btn.addEventListener('click', function() { openEditModal(this); });
        });

        // Attach click handlers to all detail buttons
        document.querySelectorAll('.detail-btn').forEach(function(btn) {
            btn.addEventListener('click', function() { openDetailModal(this); });
        });

        // Close modal bila klik di luar area
        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) closeEditModal();
        });
        document.getElementById('detailModal').addEventListener('click', function(e) {
            if (e.target === this) closeDetailModal();
        });

        // Close modal dengan tombol Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeEditModal();
                closeDetailModal();
            }
        });
    </script>
</x-app-layout>
