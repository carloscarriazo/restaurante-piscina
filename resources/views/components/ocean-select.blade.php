@props([
    'label' => null,
    'name' => null,
    'placeholder' => 'Seleccione una opción',
    'required' => false,
    'options' => [],
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
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
            <i class="{{ $icon }} text-gray-400"></i>
        </div>
        @endif

        <select
            name="{{ $name }}"
            id="{{ $name }}"
            {{ $attributes->merge(['class' => 'w-full rounded-xl border-2 border-gray-200 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 transition-all duration-200 ' . ($icon ? 'pl-10 pr-10' : 'px-4 pr-10') . ' py-2.5 text-gray-900 appearance-none bg-white']) }}
            @if($required) required @endif
        >
            <option value="">{{ $placeholder }}</option>
            {{ $slot }}
        </select>

        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
            <i class="fas fa-chevron-down text-gray-400"></i>
        </div>
    </div>

    @if($error)
    <p class="text-sm text-red-600 flex items-center gap-1">
        <i class="fas fa-exclamation-circle"></i>
        {{ $error }}
    </p>
    @endif
</div>
