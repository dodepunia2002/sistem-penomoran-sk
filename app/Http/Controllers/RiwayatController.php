<?php

namespace App\Http\Controllers;

use App\Models\Riwayat;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    public function index(Request $request)
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

        $riwayat = $query->paginate(15);
        return view('admin.riwayat', compact('riwayat'));
    }

    public function update(Request $request, Riwayat $riwayat)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string|max:500',
        ]);
        $riwayat->update($request->only('nama', 'alamat'));
        return back()->with('success', 'Data riwayat berhasil diperbarui.');
    }

    public function destroy(Riwayat $riwayat)
    {
        $riwayat->delete();
        return back()->with('success', 'Data riwayat berhasil dihapus.');
    }
}
