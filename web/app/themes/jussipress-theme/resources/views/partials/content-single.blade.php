<article @php(post_class('mx-auto max-w-3xl px-4 py-16 sm:px-6 lg:px-8 lg:py-24 h-entry'))>

    <header class="mb-10 border-b border-brand-800 pb-10">
        <h1 class="p-name text-3xl font-bold leading-tight tracking-tight text-white sm:text-4xl lg:text-5xl">
            {!! $title !!}
        </h1>
        <div class="mt-4 flex flex-wrap items-center gap-4 text-sm text-brand-400">
            @include('partials.entry-meta')
        </div>
    </header>

    <div class="entry-content e-content">
        @php(the_content())
    </div>

    @if ($pagination())
        <footer class="mt-12 border-t border-brand-800 pt-8">
            <nav class="page-nav" aria-label="Page">
                {!! $pagination !!}
            </nav>
        </footer>
    @endif

    @php(comments_template())

</article>
