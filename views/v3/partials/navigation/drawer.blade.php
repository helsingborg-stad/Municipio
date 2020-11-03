<div class="c-drawer c-drawer--right js-drawer" data-js-toggle-item="js-drawer">
    <div class="c-drawer__header">
        <button class="hamburger hamburger--drawer hamburger--stacked@sm hamburger--reverse@md hamburger--slider is-active js-close-drawer" type="button"
        aria-label="Menu" aria-controls="navigation">
            <span class="hamburger-box">
                <span class="hamburger-inner"></span>
            </span>
            <span class="hamburger-label">
                Stäng
            </span>
        </button>
    </div>
    <div class="c-drawer__body">
        @nav([
            'classList' => ['c-nav--drawer'],
            'items' => $mobileMenuItems,
            'childItemsUrl' => $homeUrlPath . '/wp-json/municipio/v1/navigation/children',
            'direction' => 'vertical',
            'includeToggle' => true
        ])
        @endnav
    </div>
</div>
<div class="drawer-overlay js-close-drawer"></div>