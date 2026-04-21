<?php

namespace App\Http\Controllers;

use App\Models\Pengajuan;
use App\Models\Riwayat;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Admin dashboard.
     *
     * Stats are cached for 60 seconds to avoid hammering the DB on every page load.
     * Cache is invalidated via the 'admin_dashboard_stats' tag when data changes.
     */
    public function admin(): View
    {
        $stats = Cache::remember('admin_dashboard_stats', 60, function () {
            // Single query with grouped counts — much more efficient than 5 separate count queries
            $statusCounts = Pengajuan::selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'pending'  THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'diterima' THEN 1 ELSE 0 END) as diterima,
                SUM(CASE WHEN status = 'ditolak'  THEN 1 ELSE 0 END) as ditolak,
                SUM(CASE WHEN status = 'diterima' AND MONTH(created_at) = MONTH(CURRENT_DATE) AND YEAR(created_at) = YEAR(CURRENT_DATE) THEN 1 ELSE 0 END) as diterima_bulan_ini
            ")->first();

            return [
                'totalPengajuan'    => (int) $statusCounts->total,
                'pending'           => (int) $statusCounts->pending,
                'diterima'          => (int) $statusCounts->diterima,
                'ditolak'           => (int) $statusCounts->ditolak,
                'diterimaBulanIni'  => (int) $statusCounts->diterima_bulan_ini,
                'totalRiwayat'      => Riwayat::count(),
                'totalUsers'        => User::count(),
            ];
        });

        $recentActivity = Riwayat::with('processor')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentActivity'));
    }

    /**
     * Petugas dashboard.
     *
     * Stats are specific per user — keyed by user ID in cache.
     */
    public function petugas(): View
    {
        $userId = auth()->id();

        $stats = Cache::remember("petugas_stats_{$userId}", 30, function () use ($userId) {
            $statusCounts = Pengajuan::selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'pending'  THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'diterima' THEN 1 ELSE 0 END) as diterima,
                SUM(CASE WHEN status = 'ditolak'  THEN 1 ELSE 0 END) as ditolak
            ")
            ->where('submitted_by', $userId)
            ->first();

            return [
                'totalPengajuan' => (int) $statusCounts->total,
                'pending'        => (int) $statusCounts->pending,
                'diterima'       => (int) $statusCounts->diterima,
                'ditolak'        => (int) $statusCounts->ditolak,
            ];
        });

        $recentActivity = Pengajuan::where('submitted_by', $userId)
            ->latest()
            ->take(5)
            ->get();

        return view('petugas.dashboard', compact('stats', 'recentActivity'));
    }
}
