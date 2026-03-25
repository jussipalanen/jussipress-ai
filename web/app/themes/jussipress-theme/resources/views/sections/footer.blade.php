<footer class="bg-brand-950 border-t border-brand-800">

    {{-- Widget area --}}
    @if (is_active_sidebar('sidebar-footer'))
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8 lg:py-16">
            <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
                @php(dynamic_sidebar('sidebar-footer'))
            </div>
        </div>
        <div class="border-t border-brand-800"></div>
    @endif

    {{-- Bottom bar --}}
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">

            <a href="{{ home_url('/') }}" class="text-sm font-semibold text-white hover:text-brand-200 transition-colors">
                {!! $siteName !!}
            </a>

            <p class="text-sm text-brand-500">
                &copy; {{ date('Y') }} {!! $siteName !!}. {{ __('All rights reserved.', 'sage') }}
            </p>

            @if (has_nav_menu('primary_navigation'))
                <nav aria-label="{{ __('Footer navigation', 'sage') }}">
                    {!! wp_nav_menu([
                        'theme_location' => 'primary_navigation',
                        'container' => false,
                        'menu_class' => 'footer-nav',
                        'echo' => false,
                        'depth' => 1,
                    ]) !!}
                </nav>
            @endif

        </div>
    </div>

</footer>
