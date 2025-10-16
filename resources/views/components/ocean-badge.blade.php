@props([
    'variant' => 'info', // info, success, warning, danger, primary
    'size' => 'md', // sm, md, lg
    'icon' => null
])

@php
$variants = [
    'info' => 'bg-gradient-to-r from-blue-100 to-cyan-100 text-blue-700 border-blue-200',
    'success' => 'bg-gradient-to-r from-emerald-100 to-teal-100 text-emerald-700 border-emerald-200',
    'warning' => 'bg-gradient-to-r from-amber-100 to-orange-100 text-amber-700 border-amber-200',
    'danger' => 'bg-gradient-to-r from-red-100 to-rose-100 text-red-700 border-red-200',
    'primary' => 'bg-gradient-to-r from-cyan-100 to-blue-100 text-cyan-700 border-cyan-200',
];

$sizes = [
    'sm' => 'px-2 py-0.5 text-xs',
    'md' => 'px-3 py-1 text-sm',
    'lg' => 'px-4 py-1.5 text-base',
];

$classes = $variants[$variant] . ' ' . $sizes[$size];
@endphp

<span {{ $attributes->merge(['class' => "$classes inline-flex items-center gap-1.5 rounded-full border font-semibold"]) }}>
    @if($icon)
        <i class="{{ $icon }}"></i>
    @endif
    {{ $slot }}
</span>
