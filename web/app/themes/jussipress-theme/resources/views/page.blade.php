@extends('layouts.app')

@section('content')
    @while (have_posts())
        @php
            the_post();
            $blocks = parse_blocks(get_the_content());
            $firstBlock = collect($blocks)->first(fn($b) => !empty($b['blockName']));
            $hasHeroFirst = $firstBlock && $firstBlock['blockName'] === 'jussipress/hero';
        @endphp
        @include('partials.page-header', compact('hasHeroFirst'))
        @includeFirst(['partials.content-page', 'partials.content'], compact('hasHeroFirst'))
    @endwhile
@endsection
