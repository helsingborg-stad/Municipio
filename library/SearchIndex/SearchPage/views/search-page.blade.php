<div class="search-index-page" data-search-index-page>
    <div class="search-index-page__toolbar">
        <label class="screen-reader-text" for="search-index-page-query">{{ $lang['searchLabel'] }}</label>
        <input id="search-index-page-query" class="search-index-page__input" type="search" data-search-index-query aria-label="{{ $lang['searchLabel'] }}">
        <button class="search-index-page__filter-toggle" type="button" data-search-index-filter-toggle>{{ $lang['openFilters'] }}</button>
    </div>

    <div class="search-index-page__layout">
        <aside class="search-index-page__facets" data-search-index-facets aria-label="{{ $lang['openFilters'] }}">
            <p data-search-index-no-facets>{{ $lang['nofacets'] }}</p>
        </aside>
        <div class="search-index-page__results">
            <p class="search-index-page__stats" data-search-index-stats aria-live="polite"></p>
            <div class="search-index-page__hits" data-search-index-hits aria-live="polite"></div>
            <nav class="search-index-page__pagination" data-search-index-pagination aria-label="{{ $lang['openFilters'] }}"></nav>
        </div>
    </div>

    <template data-search-index-hit>
        <article class="search-index-page__hit">
            <img class="search-index-page__image" data-hit-image alt="">
            <div>
                <h2 class="search-index-page__title"><a data-hit-link><span data-hit-title></span></a></h2>
                <p class="search-index-page__meta" data-hit-meta></p>
                <p data-hit-summary></p>
            </div>
        </article>
    </template>
    <template data-search-index-no-results><p class="notice notice-info">{{ $lang['noresults'] }}</p></template>
    <script type="application/json" data-search-index-lang>@json($lang)</script>
</div>