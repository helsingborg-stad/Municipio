@element([
    'componentElement' => 'section',
])
    @typography(['element' => 'h3', 'classList' => ['u-margin__bottom--2']])
        {{$lang->contact}}
    @endtypography

    @paper(['padding' => 2])
        @collection()
            @foreach ($post->getSchemaProperty('applicationContact') as $contact)
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
@endelement