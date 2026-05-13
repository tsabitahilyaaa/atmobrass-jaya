<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::where('role', 'user')->latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->orders()->delete();
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}