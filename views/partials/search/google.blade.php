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

<?php do_action('search_notices'); ?>

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
    <div class="container main-container">
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
                        <?php do_action('loop_start'); ?>

                        @if ($template === 'grid')
                        <div class="grid">
                            @foreach ($results->items as $item)
                            <?php
                            $date = apply_filters('Municipio/search_result/date', $search->getModifiedDate($item), false);
                            $permalink = apply_filters('Municipio/search_result/permalink_url', $item->link, false);
                            $permalinkText = apply_filters('Municipio/search_result/permalink_text', $item->htmlFormattedUrl, false);
                            $title = apply_filters('Municipio/search_result/title', $item->htmlTitle, false);
                            $titleClass = isset($item->fileFormat) ? $search->getFiletypeClass($item->fileFormat) : '';
                            $lead = apply_filters('Municipio/search_result/excerpt', trim($item->snippet), false);
                            $thumbnail = false;
                            ?>
                                @include('partials.search.result-item-grid')
                            @endforeach
                        </div>
                        @else
                        <ul class="search-result-list">
                            @foreach ($results->items as $item)
                            <?php
                            $date = apply_filters('Municipio/search_result/date', $search->getModifiedDate($item), false);
                            $permalink = apply_filters('Municipio/search_result/permalink_url', $item->link, false);
                            $permalinkText = apply_filters('Municipio/search_result/permalink_text', $item->htmlFormattedUrl, false);
                            $title = apply_filters('Municipio/search_result/title', $item->htmlTitle, false);
                            $titleClass = isset($item->fileFormat) ? $search->getFiletypeClass($item->fileFormat) : '';
                            $lead = apply_filters('Municipio/search_result/excerpt', trim($item->snippet), false);
                            $thumbnail = false;
                            ?>
                            <li>
                                @include('partials.search.result-item')
                            </li>
                            @endforeach

                        </ul>
                        @endif
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
