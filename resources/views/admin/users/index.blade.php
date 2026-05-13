@extends('layouts.admin')

@section('title', 'Manajemen Pengguna — Admin')

@section('content')
<h1 class="font-display font-bold text-2xl mb-6">Manajemen Pengguna</h1>

@if($users->count() === 0)
    <p class="text-muted text-sm">Belum ada pengguna terdaftar.</p>
@else
    <div class="bg-dark-100 border border-dark-300 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-dark-300 text-left text-xs text-muted uppercase tracking-wider">
                        <th class="p-4">Nama</th>
                        <th class="p-4">Email</th>
                        <th class="p-4 hidden sm:table-cell">Telepon</th>
                        <th class="p-4 hidden sm:table-cell">Terdaftar</th>
                        <th class="p-4 hidden sm:table-cell">Pesanan</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr class="border-b border-dark-300 hover:bg-dark-200 transition-colors">
                        <td class="p-4 font-medium">{{ $user->name }}</td>
                        <td class="p-4 text-muted">{{ $user->email }}</td>
                        <td class="p-4 hidden sm:table-cell text-muted">{{ $user->phone ?? '-' }}</td>
                        <td class="p-4 hidden sm:table-cell text-muted">{{ $user->created_at->format('d M Y') }}</td>
                        <td class="p-4 hidden sm:table-cell">{{ $user->orders->count() }}</td>
                        <td class="p-4 text-right">
                            <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" onsubmit="return confirm('Hapus pengguna ini dan semua pesanannya?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-muted hover:text-red-400 transition-colors"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6 flex justify-center">{{ $users->links() }}</div>
@endif
@endsection