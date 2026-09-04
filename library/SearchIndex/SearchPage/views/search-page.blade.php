<div class="search-index-page" data-search-index-page>
    <div class="search-index-page__layout">
        <div class="search-index-page__toolbar">
            <div class="search-index-page__field">
                <label class="screen-reader-text" for="search-index-page-query">{{ $lang['searchLabel'] }}</label>
                @icon([
                    'icon' => 'search',
                    'size' => 'md',
                    'classList' => ['search-index-page__search-icon']
                ])
                @endicon
                <input id="search-index-page-query" class="search-index-page__input" type="search" data-search-index-query aria-label="{{ $lang['searchLabel'] }}">
            </div>
            @button([
                'text' => $lang['openFilters'],
                'type' => 'button',
                'style' => 'basic',
                'size' => 'md',
                'icon' => 'filter_alt',
                'reversePositions' => true,
                'classList' => ['search-index-page__filter-toggle'],
                'attributeList' => [
                    'data-search-index-filter-toggle' => '',
                    'aria-pressed' => 'false'
                ]
            ])
            @endbutton
        </div>
        <aside class="search-index-page__facets" data-search-index-facets aria-label="{{ $lang['openFilters'] }}">
            <div data-search-index-facet-options>
                <p data-search-index-no-facets>{{ $lang['nofacets'] }}</p>
            </div>
            @button([
                'text' => $lang['openFilters'],
                'type' => 'button',
                'style' => 'filled',
                'size' => 'md',
                'icon' => 'filter_alt',
                'reversePositions' => true,
                'classList' => ['search-index-page__filter-apply'],
                'attributeList' => ['data-search-index-filter-close' => '']
            ])
            @endbutton
        </aside>
        <div class="search-index-page__results">
            <p class="search-index-page__stats" data-search-index-stats aria-live="polite"></p>
            <div class="search-index-page__hits" data-search-index-hits aria-live="polite"></div>
            <nav class="search-index-page__pagination" data-search-index-pagination aria-label="{{ $lang['pagination'] }}"></nav>
        </div>
    </div>

    <template data-search-index-hit>
        <article>
            <a class="search-index-page__hit" data-hit-link>
                <figure class="search-index-page__media" data-hit-media>
                    <img class="search-index-page__image" data-hit-image alt="" loading="lazy" decoding="async">
                </figure>
                <div class="search-index-page__hit-content">
                    <div class="search-index-page__hit-heading">
                        <h2 class="search-index-page__title"><span data-hit-title></span></h2>
                        <span class="search-index-page__meta" data-hit-meta></span>
                    </div>
                    <p data-hit-summary></p>
                </div>
            </a>
        </article>
    </template>
    <template data-search-index-no-results><p class="notice notice-info">{{ $lang['noresults'] }}</p></template>
    <script type="application/json" data-search-index-lang>@json($lang)</script>
</div>