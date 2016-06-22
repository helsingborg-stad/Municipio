<?php $pagination = $search->pagination(); ?>

<section class="creamy gutter-vertical gutter-lg clearfix">
    <div class="container">
        <div class="gid">
            <div class="grid-lg-12">
                {!! get_search_form() !!}
                <div class="gutter gutter-sm gutter-top">
                    <strong>{{ $results->searchInformation->formattedTotalResults }}</strong> träffar på <strong>"{{ get_search_query() }}"</strong> inom Helsingborg.se
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
                <i class="fa fa-info-circle"></i> <?php _e('Found no matching results on your search…', 'municipio'); ?>
            </div>
        </div>
    </div>
</div>

@else

<section>
    <div class="container">
        <div class="grid">
            <div class="grid-md-12 grid-lg-9">
                @if ($pagination)
                <div class="grid">
                    <div class="grid-lg-12">
                        {!! $pagination !!}
                    </div>
                </div>
                @endif

                <div class="grid">
                    <div class="grid-lg-12">
                        <ul class="search-result-list">

                            @foreach ($results->items as $item)
                            <li>
                                <div class="search-result-item">
                                    <span class="search-result-date">{{ apply_filters('Municipio/search_result/date', $search->getModifiedDate($item), false) }}</span>
                                    <h3><a href="{{ apply_filters('Municipio/search_result/permalink_url', $item->link, false) }}" class="{{ (isset($item->fileFormat)) ? $search->getFiletypeClass($item->fileFormat) : '' }}">{!! apply_filters('Municipio/search_result/title', $item->htmlTitle, false) !!}</a></h3>
                                    <p>{!! apply_filters('Municipio/search_result/excerpt', trim($item->snippet), false) !!}</p>
                                    <div class="search-result-info">
                                        <span class="search-result-url"><i class="fa fa-globe"></i> <a href="{{ apply_filters('Municipio/search_result/permalink_url', $item->link, false) }}">{!! apply_filters('Municipio/search_result/permalink_text', $item->htmlFormattedUrl, false) !!}</a></span>
                                    </div>
                                </div>
                            </li>
                            @endforeach

                        </ul>
                    </div>
                </div>

                @if ($pagination)
                <div class="grid">
                    <div class="grid-lg-12">
                        {!! $pagination !!}
                    </div>
                </div>
                @endif
            </div>

            @include('partials.sidebar-right')
        </div>
    </div>
</section>

@endif
