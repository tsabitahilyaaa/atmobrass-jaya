@session('success')
<div class="max-w-7xl mx-auto px-4 sm:px-6 mt-4">
    <div class="bg-green-900/30 border border-green-500/30 text-green-400 px-5 py-3 rounded-lg text-sm flex items-center gap-3">
        <i class="fas fa-check-circle"></i>
        <span>{{ $value }}</span>
    </div>
</div>
@endsession

@session('error')
<div class="max-w-7xl mx-auto px-4 sm:px-6 mt-4">
    <div class="bg-red-900/30 border border-red-500/30 text-red-400 px-5 py-3 rounded-lg text-sm flex items-center gap-3">
        <i class="fas fa-exclamation-circle"></i>
        <span>{{ $value }}</span>
    </div>
</div>
@endsession