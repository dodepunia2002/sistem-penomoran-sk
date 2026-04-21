<x-app-layout>
    <h2 class="page-title">INPUT DATA PENGAJUAN</h2>

    <div class="card" style="max-width: 640px;">
        <div class="card-header">
            <h3 class="card-title">📝 Form Pengajuan Nomor SK</h3>
        </div>
        <p style="font-size: 0.82rem; color: var(--text-muted); margin-bottom: 1.5rem;">
            Isi formulir di bawah ini untuk mengajukan permintaan penomoran Surat Keputusan (SK).
            Pengajuan akan ditinjau oleh Admin Dishub Gianyar.
        </p>

        <form method="POST" action="{{ route('petugas.pengajuan.store') }}" data-loading>
            @csrf

            <div class="form-group">
                <label class="form-label" for="nama">Nama <span style="color:#ef4444">*</span></label>
                <input
                    type="text"
                    id="nama"
                    name="nama"
                    class="form-input"
                    placeholder="Masukkan nama lokasi / instansi"
                    value="{{ old('nama') }}"
                    required
                    autocomplete="off"
                >
                @error('nama')
                    <div class="form-error">⚠ {{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="alamat">Alamat <span style="color:#ef4444">*</span></label>
                <input
                    type="text"
                    id="alamat"
                    name="alamat"
                    class="form-input"
                    placeholder="Masukkan alamat lengkap"
                    value="{{ old('alamat') }}"
                    required
                >
                @error('alamat')
                    <div class="form-error">⚠ {{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="tanggal">Tanggal <span style="color:#ef4444">*</span></label>
                <input
                    type="date"
                    id="tanggal"
                    name="tanggal"
                    class="form-input"
                    value="{{ old('tanggal', date('Y-m-d')) }}"
                    required
                >
                @error('tanggal')
                    <div class="form-error">⚠ {{ $message }}</div>
                @enderror
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.75rem; padding-top: 1.25rem; border-top: 1px solid var(--border);">
                <a href="{{ route('petugas.dashboard') }}" class="btn btn-cancel">
                    Batal
                </a>
                <button type="submit" id="submitBtn" class="btn btn-primary">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    Kirim Pengajuan
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
