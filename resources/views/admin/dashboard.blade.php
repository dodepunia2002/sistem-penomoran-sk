<x-app-layout>
    @php
        $hour = now()->setTimezone('Asia/Makassar')->hour;
        if ($hour >= 0 && $hour < 11)       { $greeting = '🌅 Selamat Pagi'; }
        elseif ($hour >= 11 && $hour < 15)  { $greeting = '☀️ Selamat Siang'; }
        elseif ($hour >= 15 && $hour < 18)  { $greeting = '🌇 Selamat Sore'; }
        else                                { $greeting = '🌙 Selamat Malam'; }
    @endphp

    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
        <div class="greeting">
            <h1>{{ $greeting }}, <span>{{ auth()->user()->name }}</span></h1>
            <p>📊 Dashboard Admin — Sistem Penomoran SK</p>
        </div>
        <div class="live-clock">
            <div class="clock-time" id="live-clock-time">--:--:--</div>
            <div class="clock-date" id="live-clock-date">...</div>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card" style="border-left-color: #3b82f6;">
            <div class="stat-icon" style="background: rgba(59,130,246,0.1); color: #3b82f6;">📄</div>
            <div><div class="stat-value">{{ $stats['totalPengajuan'] }}</div><div class="stat-label">Total Pengajuan</div></div>
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
        <div class="stat-card" style="border-left-color: #8b5cf6;">
            <div class="stat-icon" style="background: rgba(139,92,246,0.1); color: #8b5cf6;">📈</div>
            <div><div class="stat-value">{{ $stats['diterimaBulanIni'] }}</div><div class="stat-label">Diterima Bulan Ini</div></div>
        </div>
        <div class="stat-card" style="border-left-color: #06b6d4;">
            <div class="stat-icon" style="background: rgba(6,182,212,0.1); color: #06b6d4;">👥</div>
            <div><div class="stat-value">{{ $stats['totalUsers'] }}</div><div class="stat-label">Total User</div></div>
        </div>
    </div>

    <div class="card">
        <h3 style="margin-bottom: 1rem;">📈 Aktivitas Terakhir</h3>
        @forelse($recentActivity as $item)
            <div class="activity-item">
                <div class="activity-dot"></div>
                <div>
                    <div class="activity-main"><strong>{{ $item->nama }}</strong> mendapat Nomor SK <span class="sk-badge">{{ $item->nomor_sk }}</span></div>
                    <div class="activity-meta">{{ $item->alamat }} • Diproses oleh {{ $item->processor->name ?? 'Admin' }} • {{ $item->created_at->diffForHumans() }}</div>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <div class="empty-state-icon">📊</div>
                <p class="empty-state-text">Belum ada aktivitas.</p>
                <p class="empty-state-sub">Data terakhir akan muncul di sini.</p>
            </div>
        @endforelse
    </div>
</x-app-layout>
