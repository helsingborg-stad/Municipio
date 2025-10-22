@if (empty($hideTitle) && !empty($postTitle))
    @typography([
        'id'        => 'mod-markdown-' . $ID .'-label',
        'element'   => 'h2', 
        'variant'   => 'h2', 
        'classList' => [
            'module-title'
        ]
    ])
        {!! $postTitle !!}
    @endtypography
@endif

@if($isMarkdownUrl)

    @if($parsedMarkdown)
        {!! $parsedMarkdown !!}

        @if($showMarkdownSource && ($markdownUrl || $markdownLastUpdated))
            @includeWhen($isWrapped, 'summary.unwrapped')
            @includeUnless($isWrapped, 'summary.wrapped')
        @endif
    @else 
        @notice([
            'id' => 'mod-markdown-' . $ID .'-notice',
            'type' => 'info',
            'message' => [
                'text' => $language->fetchError,
            ],
            'icon' => [
                'name' => 'report',
                'size' => 'md',
                'color' => 'white'
            ]
        ])
        @endnotice
    @endif

@else
    @if ($wpError)
        @notice([
            'id'        => 'mod-markdown-' . $ID .'-notice',
            'type' => 'info',
            'message' => [
                'text' => $wpError->getMessage(),
            ],
            'icon' => [
                'name' => 'report',
                'size' => 'md',
                'color' => 'white'
            ]
        ])
        @endnotice
    @else 
        @notice([
            'id'        => 'mod-markdown-' . $ID .'-notice',
            'type' => 'info',
            'message' => [
                'text' => $language->parseError,
            ],
            'icon' => [
                'name' => 'report',
                'size' => 'md',
                'color' => 'white'
            ]
        ])
        @endnotice
    @endif
@endif