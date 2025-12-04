<!-- Social media by curator -->

@if (!$hideTitle && !empty($postTitle))
    @typography([
        'id' => 'mod-curator-' . $ID . '-label',
        'element' => 'h2',
        'classList' => ['module-title']
    ])
        {!! $postTitle !!}
    @endtypography
@endif

@if ($showFeed)

    <div class="o-grid modularity-socialmedia-container">
        <div class="o-grid modularity-socialmedia__content {{ $gutter }}">
            @includeIf("partials.$layout", ['posts' => $posts])
            @for ($i = 0; $i < $columns; $i++)
                <div class="modularity-socialmedia__item--placeholder u-display--none {{ $columnClasses }}">
                    @block([
                        'ratio' => $ratio,
                        'classList' => ['u-preloader', 'u-height--100']
                    ])
                    @endblock
                </div>
            @endfor
        </div>
        <div class="o-grid-12 modularity-socialmedia__footer">
            @typography(['element' => 'div', 'variant' => 'load-more', 'classList' => ['u-text-align--center']])
                @button([
                    'color' => 'primary',
                    'text' => $i18n['loadMore'],
                    'attributeList' => [
                        'data-items-per-page' => $numberOfItems,
                        'data-item-count' => $postCount,
                        'data-items-loaded' => $numberOfItems,
                        'data-code' => $embedCode,
                        'data-layout' => $layout,
                    ],
                    'classList' => ['mod-curator-load-more'],
                ])
                @endbutton
            @endtypography

            @if($showPoweredBy)
                @typography(['element' => 'div', 'variant' => 'meta', 'classList' => ['u-text-align--right']])
                    <a href="https://curator.io" target="_blank" rel="nofollow">Powered by Curator.io</a>
                @endtypography
            @endif
        </div>
    </div>
@else
    @notice([
        'type' => 'info',
        'message' => [
            'text' => $errorMessage,
            'size' => 'sm'
        ],
        'icon' => [
            'name' => 'report',
            'size' => 'md',
            'color' => 'white'
        ]
    ])
    @endnotice

@endif
