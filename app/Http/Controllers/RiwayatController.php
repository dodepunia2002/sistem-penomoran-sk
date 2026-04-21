<?php

namespace App\Http\Controllers;

use App\Models\Riwayat;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    public function index(Request $request)
    {
        $riwayat = $this->getFilteredQuery($request)->paginate(15);
        return view('admin.riwayat', compact('riwayat'));
    }

    /**
     * Get the filtered query for both index and cetak.
     */
    private function getFilteredQuery(Request $request)
    {
        $query = Riwayat::with('processor')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%")
                  ->orWhere('nomor_sk', 'like', "%{$search}%");
            });
        }

        if ($request->filled('month')) {
            $query->whereMonth('tanggal', $request->month);
        }

        if ($request->filled('year')) {
            $query->whereYear('tanggal', $request->year);
        }

        return $query;
    }

    /**
     * Print report view.
     */
    public function cetak(Request $request)
    {
        $riwayat = $this->getFilteredQuery($request)->get();
        return view('admin.report', compact('riwayat'));
    }

    public function update(Request $request, Riwayat $riwayat)
    {
        $request->validate([
            'nama'     => ['required', 'string', 'max:255'],
            'alamat'   => ['required', 'string', 'max:500'],
            'tanggal'  => ['required', 'date'],
            'nomor_sk' => ['required', 'string', 'max:100'],
        ]);

        $riwayat->update($request->only('nama', 'alamat', 'tanggal', 'nomor_sk'));

        return back()->with('success', 'Data riwayat SK berhasil diperbarui.');
    }

    public function destroy(Riwayat $riwayat)
    {
        $riwayat->delete();
        return back()->with('success', 'Data riwayat berhasil dihapus.');
    }
}
