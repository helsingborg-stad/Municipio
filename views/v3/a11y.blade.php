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
        
            @if (!empty($reviewDate))
                @paper(['padding' => 1])
                    <strong>{{ __('Review date:', 'municipio') }}</strong> {{ $reviewDate }}
                @endpaper
            @endif

            @if($compliance)
                @paper(['padding' => 1, 'classList' => ['u-margin__top--2']])
                    <strong>{{ __('Compliance level:', 'municipio') }}</strong> 
                    <span class="c-compliance-level c-compliance-level--{{ $compliance->color }}">
                        {{ $compliance->label }}
                    </span>
                @endpaper
            @endif

        @endelement 

    @endelement
@stop