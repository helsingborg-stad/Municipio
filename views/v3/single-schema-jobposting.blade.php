@extends('templates.single')

@section('article.content')

    {{$post->schemaObject['employerOverview'] ?? ''}}
    {!!$post->schemaObject['description'] ?? ''!!}

    @if($post->schemaObject['hiringOrganization']['ethicsPolicy'] ?? null)
        @paper(['padding' => 4])
            {!!$post->schemaObject['hiringOrganization']['ethicsPolicy']!!}
        @endpaper
    @endif
    
@stop

@section('sidebar-right')
    
    @typography(['element' => 'h3', 'classList' => ['u-margin__bottom--2']])
        {{$lang->information}}
    @endtypography

    @if(!empty($informationList))

        @paper(['padding' => 2])
            @collection()
                @foreach ($informationList as $item)
                    @collection__item([])
                        @typography(['element' => 'h4'])
                            {{$item['label']}}
                        @endtypography
                        @typography([])
                            {{$item['value']}}
                        @endtypography
                    @endcollection__item
                @endforeach
            @endcollection
        @endpaper

    @endif

    @if(!empty($post->schemaObject['applicationContact']))

        @typography(['element' => 'h3', 'classList' => ['u-margin__bottom--2']])
            {{$lang->contact}}
        @endtypography

        @paper(['padding' => 2])
            @collection()
                @foreach ($post->schemaObject['applicationContact'] as $contact)
                    @collection__item([])
                        
                        @if($contact['name'] ?? null)
                            @typography(['element' => 'h4'])
                                {{$contact['name']}}
                            @endtypography
                        @endif
                        
                        @if($contact['contactType'] ?? null)
                            @typography(['variant' => 'meta'])
                                {{$contact['contactType']}}
                            @endtypography
                        @endif

                        @if($contact['telephone'] ?? null)
                            @link(['href' => "tel:{$contact['telephone']}"])
                                {{$contact['telephone']}}
                            @endlink
                        @endif

                        <span></span>
                        
                        @if($contact['email'] ?? null)
                            @link(['href' => "mailto:{$contact['email']}"])
                                {{$contact['email']}}
                            @endlink
                        @endif

                    @endcollection__item
                @endforeach
            @endcollection
        @endpaper
    
    @endif

    @if($post->schemaObject['url'])
        @button([
            'classList' => ['u-margin__top--4'],
            'fullWidth' => true,
            'text' => $lang->apply,
            'color' => 'primary',
            'style' => 'filled',
            'href' => $post->schemaObject['url']
        ])@endbutton
    @endif

@stop