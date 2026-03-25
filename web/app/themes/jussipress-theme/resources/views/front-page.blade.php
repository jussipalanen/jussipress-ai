@extends('layouts.app')

{{-- ─── Hero ──────────────────────────────────────────────────────────────── --}}
@section('hero')
    @include('sections.hero')
@endsection

{{-- ─── Page content (Gutenberg blocks) ──────────────────────────────────── --}}
@section('content')
    @while (have_posts())
        @php(the_post())

        @if (get_the_content())
            <div class="entry-content mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
                @php(the_content())
            </div>
        @endif
    @endwhile
@endsection
