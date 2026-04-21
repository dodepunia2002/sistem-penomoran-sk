<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::latest()->get();

        return view('admin.manajemen-user', compact('users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(6)],
            'role'     => ['required', 'in:admin,petugas'],
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        return back()->with('success', "User '{$request->name}' berhasil ditambahkan.");
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', 'in:admin,petugas'],
        ]);

        // Protect: cannot downgrade your own admin role
        if ($user->id === auth()->id() && $request->role !== 'admin') {
            return back()->with('error', 'Anda tidak dapat mengubah role akun sendiri.');
        }

        $user->update($request->only('name', 'role'));

        return back()->with('success', "Data user '{$user->name}' berhasil diperbarui.");
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menghapus akun Anda sendiri.');
        }

        $name = $user->name;
        $user->delete();

        return back()->with('success', "User '{$name}' berhasil dihapus.");
    }
}
