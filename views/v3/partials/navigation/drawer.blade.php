<div class="c-drawer c-drawer--right c-drawer--primary js-drawer" data-js-toggle-item="js-drawer">
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
        @includeIf('partials.navigation.mobile', ['menuItems' => $mobileMenuItems])
    </div>
</div>
<div class="drawer-overlay js-close-drawer"></div>