@extends('layouts.admin')

@section('title', 'Pesan Kontak — Admin')

@section('content')
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
    <div>
        <h1 class="font-display font-bold text-2xl">Pesan Kontak</h1>
        <p class="text-sm text-muted mt-1">Daftar pesan yang dikirim melalui formulir kontak website.</p>
    </div>
</div>

@if($messages->count() === 0)
    <p class="text-muted">Belum ada pesan masuk.</p>
@else
    <div class="bg-dark-100 border border-dark-300 rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-[#161616]">
                    <tr class="text-left text-xs text-muted uppercase tracking-wider">
                        <th class="p-4">Nama</th>
                        <th class="p-4">Email</th>
                        <th class="p-4">Subjek</th>
                        <th class="p-4 hidden lg:table-cell">Pesan</th>
                        <th class="p-4 hidden xl:table-cell">Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($messages as $message)
                    <tr class="border-b border-dark-300 hover:bg-dark-200 transition-colors">
                        <td class="p-4 font-medium">{{ $message->name }}</td>
                        <td class="p-4 text-muted">{{ $message->email }}</td>
                        <td class="p-4 text-muted">{{ $message->subject }}</td>
                        <td class="p-4 hidden lg:table-cell text-sm text-muted">{{ Str::limit($message->message, 100) }}</td>
                        <td class="p-4 hidden xl:table-cell text-muted">{{ $message->created_at->format('d M Y H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6 flex justify-center">{{ $messages->links() }}</div>
@endif
@endsection
