<section class="relative flex min-h-svh items-center bg-brand-900 pt-16">

    {{-- Background glow blobs --}}
    <div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
        <div class="absolute -right-32 -top-40 h-150 w-150 rounded-full bg-brand-700/30 blur-3xl"></div>
        <div class="absolute -bottom-40 -left-32 h-125 w-125 rounded-full bg-brand-600/20 blur-3xl"></div>
    </div>

    <div class="relative mx-auto max-w-7xl px-4 py-24 sm:px-6 lg:px-8 lg:py-32">
        <div class="max-w-3xl">

            <p class="mb-4 text-sm font-semibold uppercase tracking-widest text-brand-300">
                {{ __('Welcome', 'sage') }}
            </p>

            <h1 class="text-4xl font-bold leading-tight tracking-tight text-white sm:text-5xl lg:text-7xl">
                {{ __('Your Powerful Headline Goes Here', 'sage') }}
            </h1>

            <p class="mt-6 max-w-2xl text-lg leading-relaxed text-brand-200 sm:text-xl">
                {{ __('A short, compelling description that tells visitors what this site is about and why they should care. Keep it concise and impactful.', 'sage') }}
            </p>

            <div class="mt-10 flex flex-wrap gap-4">
                <a href="{{ home_url('/') }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-brand-400 px-6 py-3 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-brand-300">
                    {{ __('Get Started', 'sage') }}
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                        aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </a>
                <a href="{{ home_url('/') }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-brand-600 px-6 py-3 text-sm font-semibold text-brand-200 transition-colors hover:border-brand-400 hover:text-white">
                    {{ __('Learn More', 'sage') }}
                </a>
            </div>

        </div>
    </div>

</section>
