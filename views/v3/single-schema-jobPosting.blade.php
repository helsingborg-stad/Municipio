@extends('templates.single')

@section('article.content')

    {{$post->schemaObject['employerOverview'] ?? ''}}
    {!!$post->schemaObject['description'] ?? ''!!}

    @if($post->schemaObject['hiringOrganization']['ethicsPolicy'] ?? null)
        @card(['content' => $post->schemaObject['hiringOrganization']['ethicsPolicy']])@endcard
    @endif
    
@stop

@section('sidebar-right')
    
    @typography(['element' => 'h3'])
        {{$lang->information}}
    @endtypography

    @if(!empty($informationList))

        @collection(['bordered' => true])

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

    @endif

    @if(!empty($post->schemaObject['applicationContact']))

        @typography(['element' => 'h3'])
            {{$lang->contact}}
        @endtypography

        @collection(['bordered' => true])
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
                        @link(['href' => "mailto:{$contact['telephone']}"])
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