<header class="fixed inset-x-0 top-0 z-50 h-16 bg-brand-900/95 backdrop-blur-sm border-b border-brand-800">
    <div class="mx-auto flex h-full max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">

        {{-- Logo / site name --}}
        <a href="{{ home_url('/') }}"
            class="text-lg font-bold tracking-tight text-white hover:text-brand-200 transition-colors">
            {!! $siteName !!}
        </a>

        {{-- Desktop navigation --}}
        @if (has_nav_menu('primary_navigation'))
            <nav class="nav-primary hidden lg:block" aria-label="{{ wp_get_nav_menu_name('primary_navigation') }}">
                {!! wp_nav_menu([
                    'theme_location' => 'primary_navigation',
                    'container' => false,
                    'echo' => false,
                    'depth' => 1,
                ]) !!}
            </nav>
        @endif

        {{-- Hamburger button (mobile / tablet) --}}
        <button id="menu-open" type="button"
            class="lg:hidden flex h-10 w-10 items-center justify-center rounded-md text-brand-200 hover:bg-brand-800 hover:text-white transition-colors"
            aria-label="{{ __('Open menu', 'sage') }}" aria-expanded="false" aria-controls="megamenu">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"
                aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
        </button>

    </div>
</header>

{{-- ─── Megamenu overlay (mobile / tablet) ──────────────────────────── --}}
<div id="megamenu"
    class="fixed inset-0 z-40 flex translate-x-full flex-col overflow-y-auto bg-brand-950 transition-transform duration-300 lg:hidden"
    aria-hidden="true">
    {{-- Megamenu header row --}}
    <div class="flex h-16 shrink-0 items-center justify-between border-b border-brand-800 px-4 sm:px-6">
        <a href="{{ home_url('/') }}" class="text-lg font-bold tracking-tight text-white">
            {!! $siteName !!}
        </a>
        <button id="menu-close" type="button"
            class="flex h-10 w-10 items-center justify-center rounded-md text-brand-200 hover:bg-brand-800 hover:text-white transition-colors"
            aria-label="{{ __('Close menu', 'sage') }}">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"
                aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    {{-- Megamenu page links --}}
    <nav class="flex-1 px-4 sm:px-6 py-8" aria-label="{{ __('Site pages', 'sage') }}">
        @if (has_nav_menu('primary_navigation'))
            {!! wp_nav_menu([
                'theme_location' => 'primary_navigation',
                'menu_class' => 'megamenu-nav',
                'container' => false,
                'echo' => false,
            ]) !!}
        @else
            @php($pages = get_pages(['sort_column' => 'menu_order']))
            @if ($pages)
                <ul class="megamenu-nav">
                    @foreach ($pages as $page)
                        <li class="{{ get_queried_object_id() === $page->ID ? 'current-menu-item' : '' }}">
                            <a href="{{ get_permalink($page->ID) }}">{{ $page->post_title }}</a>
                        </li>
                    @endforeach
                </ul>
            @endif
        @endif
    </nav>
</div>
