@extends($wp_parent_theme . '.views.templates.master')

@section('content')

<section class="creamy gutter-vertical gutter-lg clearfix">
    <div class="container">
        <div class="gid">
            <div class="grid-lg-12">
                {!! get_search_form() !!}
                <div class="gutter gutter-sm gutter-top">
                    <strong>{{ $results->searchInformation->formattedTotalResults }}</strong> träffar på <strong>"{{ urldecode(stripslashes($results->queries->request[0]->searchTerms)) }}"</strong> inom Helsingborg.se
                </div>
            </div>
        </div>
    </div>
</section>

@if (!$results->items)

<div class="container gutter gutter-lg gutter-top">
    <div class="grid gutter gutter-lg gutter-top">
        <div class="grid-lg-12">
            <div class="notice info">
                <i class="fa fa-info-circle"></i> Inga sökträffar…
            </div>
        </div>
    </div>
</div>

@else

<section>
    <div class="container">
        <div class="grid">
            <div class="grid-md-12 grid-lg-9">
                @if (count($results->items) > 0)
                <div class="grid">
                    <div class="grid-lg-12">
                        {!! $search->pagination() !!}
                    </div>
                </div>
                @endif

                <div class="grid">
                    <div class="grid-lg-12">
                        <ul class="search-result-list">
                            @foreach ($results->items as $item)
                            <li>
                                <div class="search-result-item">
                                    <span class="search-result-date">{{ $search->getModifiedDate($item) }}</span>
                                    <h3><a href="{{ $item->link }}" class="{{ $search->getFiletypeClass($item->fileFormat) }}">{!! $item->htmlTitle !!}</a></h3>
                                    <p>{!! trim($item->snippet) !!}</p>
                                    <div class="search-result-info">
                                        <span class="search-result-url"><i class="fa fa-globe"></i> <a href="{{ $item->link }}">{!! $item->htmlFormattedUrl !!}</a></span>
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                @if (strlen($query) > 0)
                <div class="grid">
                    <div class="grid-lg-12">
                        {!! $search->pagination() !!}
                    </div>
                </div>
                @endif
            </div>

            @include($wp_parent_theme . '.views.partials.sidebar-right')
        </div>
    </div>
</section>

@endif

@stop
