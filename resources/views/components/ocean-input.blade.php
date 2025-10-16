@props([
    'label' => null,
    'name' => null,
    'type' => 'text',
    'placeholder' => '',
    'required' => false,
    'icon' => null,
    'error' => null
])

<div class="space-y-2">
    @if($label)
    <label for="{{ $name }}" class="block text-sm font-semibold text-gray-700">
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>
    @endif

    <div class="relative">
        @if($icon)
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <i class="{{ $icon }} text-gray-400"></i>
        </div>
        @endif

        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $name }}"
            placeholder="{{ $placeholder }}"
            {{ $attributes->merge(['class' => 'w-full rounded-xl border-2 border-gray-200 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 transition-all duration-200 ' . ($icon ? 'pl-10 pr-4' : 'px-4') . ' py-2.5 text-gray-900']) }}
            @if($required) required @endif
        >
    </div>

    @if($error)
    <p class="text-sm text-red-600 flex items-center gap-1">
        <i class="fas fa-exclamation-circle"></i>
        {{ $error }}
    </p>
    @endif
</div>
