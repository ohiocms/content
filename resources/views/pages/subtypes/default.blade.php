@php
    $page = $page ?? new \Belt\Content\Page();
@endphp

<div class="container">

    @include('belt-core::layouts.web.partials.jumbotron', ['paramable' => $page])

    <div class="page-header">

        @if( $heading = $page->param('heading') )
            <div>
                <h1>{!! $heading !!}</h1>
                @if( $subheading = trim($page->param('subheading')) )
                    <p class="lead">{!! $subheading !!}</p>
                @endif
            </div>
        @endif

        @if( $body = $page->param('body') )
            <div class="general-content-wrap">
                <div class="general-content">
                    {!! $body !!}
                </div>
            </div>
        @endif

        @foreach($page->sections as $section)
            @include($section->subtype_view, ['section' => $section])
        @endforeach

    </div>
</div>