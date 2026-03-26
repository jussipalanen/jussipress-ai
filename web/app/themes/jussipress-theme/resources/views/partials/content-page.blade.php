<div class="entry-content pb-16 lg:pb-24">
    @php(the_content())

    @if ($pagination())
        <nav class="mt-12 border-t border-brand-800 pt-8" aria-label="Page">
            {!! $pagination !!}
        </nav>
    @endif
</div>
