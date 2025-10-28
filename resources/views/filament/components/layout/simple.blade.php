@php
    use Filament\Support\Enums\Width;

    $livewire ??= null;

    $renderHookScopes = $livewire?->getRenderHookScopes();
    $maxContentWidth ??= (filament()->getSimplePageMaxContentWidth() ?? Width::Large);

    if (is_string($maxContentWidth)) {
        $maxContentWidth = Width::tryFrom($maxContentWidth) ?? $maxContentWidth;
    }
@endphp

<x-filament-panels::layout.base :livewire="$livewire">
    @props([
        'after' => null,
        'heading' => null,
        'subheading' => null,
    ])

    <div class="fi-simple-layout">
        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::SIMPLE_LAYOUT_START, scopes: $renderHookScopes) }}

        @if (($hasTopbar ?? true) && filament()->auth()->check())
            <div class="fi-simple-layout-header">
                @if (filament()->hasDatabaseNotifications())
                    @livewire(Filament\Livewire\DatabaseNotifications::class, [
                        'lazy' => filament()->hasLazyLoadedDatabaseNotifications(),
                        'position' => \Filament\Enums\DatabaseNotificationsPosition::Topbar,
                    ])
                @endif

                @if (filament()->hasUserMenu())
                    @livewire(Filament\Livewire\SimpleUserMenu::class)
                @endif
            </div>
        @endif

        <div class="absolute inset-0 z-0 bg-[linear-gradient(to_right,theme(colors.primary.200)_1px,transparent_1px),linear-gradient(to_bottom,theme(colors.primary.200)_1px,transparent_1px)] bg-[size:20px_20px] bg-[position:0_0,0_0] [mask-image:repeating-linear-gradient(to_right,black_0px,black_3px,transparent_3px,transparent_8px),repeating-linear-gradient(to_bottom,black_0px,black_3px,transparent_3px,transparent_8px),radial-gradient(ellipse_70%_60%_at_50%_0%,black_60%,transparent_100%)] [-webkit-mask-image:repeating-linear-gradient(to_right,black_0px,black_3px,transparent_3px,transparent_8px),repeating-linear-gradient(to_bottom,black_0px,black_3px,transparent_3px,transparent_8px),radial-gradient(ellipse_70%_60%_at_50%_0%,black_60%,transparent_100%)] [mask-composite:intersect] [-webkit-mask-composite:source-in] dark:bg-[linear-gradient(to_right,theme(colors.primary.900)_1px,transparent_1px),linear-gradient(to_bottom,theme(colors.primary.900)_1px,transparent_1px)]">
        </div>

        <div class="fi-simple-main-ctn p-4">
            <main
                @class([
                    'fi-simple-main',
                    ($maxContentWidth instanceof Width) ? "fi-width-{$maxContentWidth->value}" : $maxContentWidth,
                    'relative grid md:grid-cols-2 overflow-hidden'
                ])
            >
                <x-image.fik-entrance-sign />

                {{ $slot }}
            </main>
        </div>

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::FOOTER, scopes: $renderHookScopes) }}

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::SIMPLE_LAYOUT_END, scopes: $renderHookScopes) }}
    </div>
</x-filament-panels::layout.base>
