@props([
    'type' => 'info', // info, success, warning, danger
    'icon' => null,
    'dismissible' => false
])

@php
$types = [
    'info' => [
        'gradient' => 'from-blue-400 to-cyan-500',
        'bg' => 'bg-blue-50',
        'border' => 'border-blue-200',
        'text' => 'text-blue-800',
        'icon' => 'fas fa-info-circle'
    ],
    'success' => [
        'gradient' => 'from-emerald-400 to-teal-500',
        'bg' => 'bg-emerald-50',
        'border' => 'border-emerald-200',
        'text' => 'text-emerald-800',
        'icon' => 'fas fa-check-circle'
    ],
    'warning' => [
        'gradient' => 'from-amber-400 to-orange-500',
        'bg' => 'bg-amber-50',
        'border' => 'border-amber-200',
        'text' => 'text-amber-800',
        'icon' => 'fas fa-exclamation-triangle'
    ],
    'danger' => [
        'gradient' => 'from-red-400 to-rose-500',
        'bg' => 'bg-red-50',
        'border' => 'border-red-200',
        'text' => 'text-red-800',
        'icon' => 'fas fa-times-circle'
    ]
];

$config = $types[$type];
$displayIcon = $icon ?? $config['icon'];
@endphp

<div
    {{ $attributes->merge(['class' => "relative rounded-xl border-2 {$config['border']} {$config['bg']} p-4"]) }}
    x-data="{ show: true }"
    x-show="show"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0 transform scale-90"
    x-transition:enter-end="opacity-100 transform scale-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
>
    <div class="flex items-start gap-3">
        <div class="flex-shrink-0">
            <div class="w-10 h-10 bg-gradient-to-r {{ $config['gradient'] }} rounded-lg flex items-center justify-center">
                <i class="{{ $displayIcon }} text-white"></i>
            </div>
        </div>

        <div class="flex-1 {{ $config['text'] }}">
            {{ $slot }}
        </div>

        @if($dismissible)
        <button
            @click="show = false"
            class="flex-shrink-0 {{ $config['text'] }} hover:opacity-70 transition-opacity"
        >
            <i class="fas fa-times"></i>
        </button>
        @endif
    </div>
</div>
