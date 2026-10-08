@extends('templates.single')

@section('content')
    @scope(['name' => ['template', 'a11y']])
        @element([
            'componentElement' => 'article',
            'id' => 'article',
            'classList' => array_merge(
                (isset($centerContent) && $centerContent) ? ['u-margin__x--auto'] : [],
                [ 'c-article', 'c-article--readable-width', 's-article', 'u-clearfix', 't-a11y-page']
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

            {{-- Status pills start --}}
            @element(['classList' => 'u-display--flex u-flex--row u-align-items--center'])
                
                {{-- Compliance level information --}}
                @if($compliance && $compliance->label)
                    @element([
                        'componentElement' => 'span', 
                        'attributeList' => [
                            'data-tooltip' => $compliance->reference->standard . ", " .$compliance->reference->version,
                        ]
                    ])
                        @element([
                            'componentElement' => 'span', 
                            'classList' => $compliance->class ?? [],
                        ])
                            <span>@icon(['icon' => $compliance->icon])@endicon {{ $compliance->label }}</span>
                        @endelement
                    @endelement
                @endif

                {{-- Review date information --}}
                @if($review && $review->date)
                    @element([
                        'componentElement' => 'span', 
                        'attributeList' => [
                            'data-tooltip' => $review->label
                        ]
                    ])
                        @element([
                            'componentElement' => 'span', 
                            'classList' => $review->class ?? [],
                        ])
                            <span>@icon(['icon' => $review->icon])@endicon {{ $review->date }}</span>
                        @endelement
                    @endelement
                @endif

            @endelement 
            {{-- Status pills end --}}

            {{-- Issues section start --}}
            @if (!empty($categorizedIssues) && is_array($categorizedIssues) && count($categorizedIssues) > 0)
                @iconSection(['gap' => 4])
                    @foreach($categorizedIssues as $category)
                        @iconSection__item([
                            'icon' => ['icon' => $category['icon'] ?? 'accessible'],
                        ])
                            @typography(['element' => 'h2', 'variant' => 'h4'])
                                {{ $category['label'] }}
                            @endtypography
                            @element(['classList' => ['u-margin__top--2']])
                                <ul>
                                    @foreach ($category['issues'] as $issue)
                                        <li>{{ $issue['label'] }}</li>
                                    @endforeach
                                </ul>
                            @endelement
                        @endiconSection__item
                    @endforeach
                @endiconSection
            @endif
            {{-- Issues section end --}}

            @if (!empty($reportForm))
                <div class="u-margin__top--4">
                    {!! $reportForm !!}
                </div>
            @endif

        @endelement
    @endscope
@stop
