<div class="entry-content mx-auto max-w-3xl px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
    @php(the_content())

    @if ($pagination())
        <nav class="mt-12 border-t border-brand-800 pt-8" aria-label="Page">
            {!! $pagination !!}
        </nav>
    @endif
</div>
