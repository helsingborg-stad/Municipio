@extends('templates.single')

@section('content')

    @element([
        'componentElement' => 'article',
        'id' => 'article',
        'classList' => array_merge(
            (isset($centerContent) && $centerContent) ? ['u-margin__x--auto'] : [],
            [ 'c-article', 'c-article--readable-width', 's-article', 'u-clearfix' ]
        ),
    ])
        @typography([
            'element' => 'h1', 
            'variant' => 'h1', 
            'id' => 'page-title',
        ])
            {!! $heading !!}
        @endtypography

        {!! $content !!}

        @element(['classList' => 'u-display--flex u-flex--row u-align-items--center'])
            @if($compliance)
                @paper(['padding' => 1, 'classList' => ['u-margin__top--2']])
                    @element([
                        'componentElement' => 'strong',
                    ])
                        {{ $lang->complianceLevel }}:
                    @endelement
                    @element([
                        'componentElement' => 'span',
                        'classList' => [$compliance->color],
                    ])
                        {{ $compliance->label }} {{ $lang->with }} {{ $compliance->reference->standard }}, {{ $compliance->reference->version }}
                    @endelement
                @endpaper
            @endif
        @endelement 

        @if (!empty($categorizedIssues) && is_array($categorizedIssues) && count($categorizedIssues) > 0)
            @foreach($categorizedIssues as $key => $category)

                @paper(['padding' => 2, 'classList' => ['u-margin__top--2']])
                    @typography(['element' => 'h4', 'variant' => 'h4'])
                        @icon(['icon' => $category['icon'] ?? ''])@endicon
                        {{ $category['label'] }}
                    @endtypography
                    @element(['classList' => 'u-margin__top--2'])
                        <ul>
                            @foreach ($category['issues'] as $issue)
                                <li>{{ $issue['label'] }}</li>
                            @endforeach
                        </ul>
                    @endelement
                @endpaper
            @endforeach
        @endif

         @if (!empty($reviewDate))
            @typography(['element' => 'p', 'variant' => 'meta'])
                @element(['componentElement' => 'strong'])
                    {{ $lang->reviewDate }}: 
                @endelement
                {{ $reviewDate }}
            @endtypography
        @endif

    @endelement
@stop