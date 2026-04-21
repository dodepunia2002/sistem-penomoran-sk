<x-app-layout>
    @php $hour = now()->hour; $greeting = $hour < 11 ? '🌅 Selamat Pagi' : ($hour < 15 ? '☀️ Selamat Siang' : ($hour < 18 ? '🌇 Selamat Sore' : '🌙 Selamat Malam')); @endphp

    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
        <div class="greeting">
            <h1>{{ $greeting }}, <span>{{ auth()->user()->name }}</span></h1>
            <p>📋 Dashboard Petugas — Sistem Penomoran SK</p>
        </div>
        <div class="live-clock">
            <div class="clock-time" id="live-clock-time">--:--:--</div>
            <div class="clock-date" id="live-clock-date">...</div>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card" style="border-left-color: #3b82f6;">
            <div class="stat-icon" style="background: rgba(59,130,246,0.1); color: #3b82f6;">📄</div>
            <div><div class="stat-value">{{ $stats['totalPengajuan'] }}</div><div class="stat-label">Total Pengajuan Saya</div></div>
        </div>
        <div class="stat-card" style="border-left-color: #f59e0b;">
            <div class="stat-icon" style="background: rgba(245,158,11,0.1); color: #f59e0b;">⏳</div>
            <div><div class="stat-value">{{ $stats['pending'] }}</div><div class="stat-label">Menunggu Verifikasi</div></div>
        </div>
        <div class="stat-card" style="border-left-color: #22c55e;">
            <div class="stat-icon" style="background: rgba(34,197,94,0.1); color: #22c55e;">✅</div>
            <div><div class="stat-value">{{ $stats['diterima'] }}</div><div class="stat-label">Diterima</div></div>
        </div>
        <div class="stat-card" style="border-left-color: #ef4444;">
            <div class="stat-icon" style="background: rgba(239,68,68,0.1); color: #ef4444;">❌</div>
            <div><div class="stat-value">{{ $stats['ditolak'] }}</div><div class="stat-label">Ditolak</div></div>
        </div>
    </div>

    {{-- Progress Bar --}}
    @if($stats['totalPengajuan'] > 0)
    <div class="card">
        <h3 style="margin-bottom: 1rem;">📊 Progress Pengajuan</h3>
        <div style="display: flex; height: 14px; border-radius: 999px; overflow: hidden; background: #e5e7eb;">
            <div style="width: {{ ($stats['diterima']/$stats['totalPengajuan'])*100 }}%; background: #22c55e;"></div>
            <div style="width: {{ ($stats['pending']/$stats['totalPengajuan'])*100 }}%; background: #f59e0b;"></div>
            <div style="width: {{ ($stats['ditolak']/$stats['totalPengajuan'])*100 }}%; background: #ef4444;"></div>
        </div>
        <div style="display: flex; gap: 1.5rem; margin-top: 0.75rem; flex-wrap: wrap; font-size: 0.8rem; color: #6b7280;">
            <span>🟢 Diterima ({{ $stats['diterima'] }})</span>
            <span>🟡 Pending ({{ $stats['pending'] }})</span>
            <span>🔴 Ditolak ({{ $stats['ditolak'] }})</span>
        </div>
    </div>
    @endif

    <div class="card">
        <h3 style="margin-bottom: 1rem;">📋 Pengajuan Terakhir</h3>
        @forelse($recentActivity as $item)
            <div class="activity-item">
                <div class="activity-dot" style="background: {{ $item->status === 'diterima' ? '#22c55e' : ($item->status === 'ditolak' ? '#ef4444' : '#f59e0b') }}"></div>
                <div>
                    <div class="activity-main"><strong>{{ $item->nama }}</strong> — {{ $item->alamat }} <span class="badge badge-{{ $item->status === 'diterima' ? 'accepted' : ($item->status === 'ditolak' ? 'rejected' : 'pending') }}">{{ strtoupper($item->status) }}</span></div>
                    <div class="activity-meta">{{ $item->tanggal }}</div>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <div class="empty-state-icon">📊</div>
                <p class="empty-state-text">Belum ada pengajuan.</p>
            </div>
        @endforelse
    </div>
</x-app-layout>
