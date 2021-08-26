@if (!empty($quicklinksMenuItems) && $isFrontPage)
    @header([
        'id'                => 'sticky-header',
        'textColor'         => $quicklinksOptions->textColor,
        'backgroundColor'   => $quicklinksOptions->backgroundColor,
        'classList' => [
            'site-header'
        ],
    ])
        <div class="c-header__menu c-header__menu--secondary u-padding--05 u-print-display--none">
            <div class="o-container">
                <nav role="navigation" aria-label="{{ $lang->primaryNavigation }}">
                    @nav([
                        'items' => $quicklinksMenuItems,
                        'direction' => 'horizontal',
                        'classList' => ['u-flex-wrap--no-wrap', 'u-justify-content--space-between']
                    ])
                    @endnav
                </nav>
            </div>
        </div>
    @endheader
@endif
