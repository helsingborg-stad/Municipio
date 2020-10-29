<div class="c-drawer c-drawer--right js-drawer" data-js-toggle-item="js-drawer">
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
<div class="overlay js-close-drawer"></div>