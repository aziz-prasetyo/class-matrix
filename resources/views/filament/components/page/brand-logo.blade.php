@php
    $logoUrl = asset('images/logo/upnvj.png');
    $brandName = filament()->getBrandName();
@endphp

@if(! auth()->check())
    <div class="space-y-2">
        <img
            alt="{{ __('filament-panels::layout.logo.alt', ['name' => $brandName]) }}"
            src="{{ $logoUrl }}"
            class="h-40 mx-auto"
        />

        <h1 class="text-2xl font-extrabold text-center bg-gradient-to-r from-primary-600 to-amber-500 bg-clip-text text-transparent">{{ $brandName }}</h1>

        <span class="block w-full h-px max-w-6xl mx-auto my-4 py-0.5 bg-gradient-to-r from-transparent via-primary-500 to-transparent">
        </span>
    </div>
@else
    <div class="flex gap-2 items-center">
        <img
            alt="{{ __('filament-panels::layout.logo.alt', ['name' => $brandName]) }}"
            src="{{ $logoUrl }}"
            class="h-9"
        />

        <h1 class="text-xl font-extrabold text-center bg-gradient-to-r from-primary-600 to-amber-500 bg-clip-text text-transparent">{{ $brandName }}</h1>
    </div>
@endif
