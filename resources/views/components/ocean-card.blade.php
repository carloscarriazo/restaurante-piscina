@props([
    'title' => null,
    'icon' => null,
    'gradient' => 'from-cyan-400 to-blue-500',
    'padding' => 'p-6'
])

<div class="group relative">
    <div class="absolute -inset-1 bg-gradient-to-r {{ $gradient }} rounded-2xl blur opacity-25 group-hover:opacity-75 transition duration-300"></div>
    <div class="relative bg-white/80 backdrop-blur-sm rounded-2xl border border-white/50 shadow-lg {{ $padding }} hover:shadow-2xl transition-all duration-300">
        @if($title || $icon)
        <div class="flex items-center mb-4">
            @if($icon)
            <div class="w-12 h-12 bg-gradient-to-r {{ $gradient }} rounded-xl flex items-center justify-center shadow-lg mr-3">
                <i class="{{ $icon }} text-white text-xl"></i>
            </div>
            @endif
            @if($title)
            <h3 class="text-xl font-bold bg-gradient-to-r {{ $gradient }} bg-clip-text text-transparent">
                {{ $title }}
            </h3>
            @endif
        </div>
        @endif

        {{ $slot }}
    </div>
</div>
