<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePengajuanRequest;
use App\Models\Pengajuan;
use App\Models\Riwayat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PengajuanController extends Controller
{
    /**
     * Admin: daftar semua pengajuan pending.
     * Petugas: riwayat pengajuan milik sendiri.
     */
    public function index(): View
    {
        if (auth()->user()->isAdmin()) {
            $pengajuan = Pengajuan::with('submitter')
                ->latest()
                ->get();

            return view('admin.pemberian-nomor', compact('pengajuan'));
        }

        // Petugas — lihat riwayat pengajuan sendiri (with nomor SK jika sudah diterima)
        $pengajuan = Pengajuan::with(['riwayat', 'riwayat.processor'])
            ->where('submitted_by', auth()->id())
            ->latest()
            ->get();

        return view('petugas.riwayat', compact('pengajuan'));
    }

    /**
     * Petugas: tampilkan form input pengajuan baru.
     */
    public function create(): View
    {
        return view('petugas.input-data');
    }

    /**
     * Petugas: simpan pengajuan baru.
     */
    public function store(StorePengajuanRequest $request): RedirectResponse
    {
        Pengajuan::create([
            'nama'         => $request->nama,
            'alamat'       => $request->alamat,
            'tanggal'      => $request->tanggal,
            'submitted_by' => auth()->id(),
        ]);

        // Invalidate caches
        Cache::forget('admin_dashboard_stats');
        Cache::forget("petugas_stats_" . auth()->id());

        return redirect()
            ->route('petugas.riwayat')
            ->with('success', 'Pengajuan berhasil dikirim! Menunggu verifikasi admin.');
    }

    /**
     * Petugas: update pengajuan yang masih pending.
     */
    public function update(Request $request, Pengajuan $pengajuan): RedirectResponse
    {
        // Pastikan petugas hanya bisa edit miliknya sendiri
        if (! auth()->user()->isAdmin() && $pengajuan->submitted_by !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit pengajuan ini.');
        }

        $request->validate([
            'nama'   => ['required', 'string', 'max:255'],
            'alamat' => ['required', 'string', 'max:500'],
        ]);

        if ($pengajuan->status !== 'pending') {
            return back()->with('error', 'Hanya pengajuan dengan status pending yang bisa diubah.');
        }

        $pengajuan->update($request->only('nama', 'alamat'));

        return back()->with('success', 'Data pengajuan berhasil diperbarui.');
    }

    /**
     * Petugas: hapus pengajuan yang masih pending.
     */
    public function destroy(Pengajuan $pengajuan): RedirectResponse
    {
        if (! auth()->user()->isAdmin() && $pengajuan->submitted_by !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus pengajuan ini.');
        }

        if ($pengajuan->status !== 'pending') {
            return back()->with('error', 'Hanya pengajuan dengan status pending yang bisa dihapus.');
        }

        $pengajuan->delete();

        return back()->with('success', 'Pengajuan berhasil dihapus.');
    }

    /**
     * Admin: terima pengajuan dan beri nomor SK.
     *
     * Race-condition safe: menggunakan DB transaction + pessimistic locking
     * agar dua request bersamaan tidak menghasilkan nomor SK duplikat.
     */
    public function terima(Pengajuan $pengajuan): RedirectResponse
    {
        if ($pengajuan->status !== 'pending') {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        try {
            $nomorSK = DB::transaction(function () use ($pengajuan) {
                // Lock row agar tidak ada race condition
                $locked = Pengajuan::lockForUpdate()->findOrFail($pengajuan->id);

                if ($locked->status !== 'pending') {
                    throw new \RuntimeException('already_processed');
                }

                $now   = now();
                $count = Riwayat::whereMonth('created_at', $now->month)
                    ->whereYear('created_at', $now->year)
                    ->lockForUpdate()
                    ->count() + 1;

                $nomorSK = sprintf('SK/%s/%04d/%s', $now->format('m'), $count, $now->format('Y'));

                $locked->update(['status' => 'diterima']);

                Riwayat::create([
                    'pengajuan_id' => $locked->id,
                    'nama'         => $locked->nama,
                    'alamat'       => $locked->alamat,
                    'tanggal'      => $locked->tanggal,
                    'nomor_sk'     => $nomorSK,
                    'processed_by' => auth()->id(),
                ]);

                return $nomorSK;
            });

            // Invalidate all dashboard caches
            Cache::forget('admin_dashboard_stats');
            Cache::forget("petugas_stats_{$pengajuan->submitted_by}");

            return back()->with('success', "✅ Pengajuan diterima! Nomor SK: {$nomorSK}");
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'already_processed') {
                return back()->with('error', 'Pengajuan sudah diproses oleh pengguna lain.');
            }
            throw $e;
        }
    }

    /**
     * Admin: tolak pengajuan.
     */
    public function tolak(Pengajuan $pengajuan): RedirectResponse
    {
        if ($pengajuan->status !== 'pending') {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        $pengajuan->update(['status' => 'ditolak']);

        // Invalidate all dashboard caches
        Cache::forget('admin_dashboard_stats');
        Cache::forget("petugas_stats_{$pengajuan->submitted_by}");

        return back()->with('success', 'Pengajuan telah ditolak.');
    }
}
