@includeWhen(empty($hideTitle) && !empty($postTitle), 'partials.post-title')
@collection([
    'classList' => ['c-collection', 'o-grid', 'o-grid--horizontal', !empty($stretch) ? ' o-grid--stretch' : ''],
])
    @if (!empty($manualInputs))
        @foreach ($manualInputs as $input)
            @collection__item([
                'link' => $input['link'],
                'classList' => array_merge($input['classList'] ?? [], [$input['columnSize']]),
                'context' => $context,
                'containerAware' => true,
                'bordered' => true,
                'attributeList' => [
                    ...($input['attributeList'] ?? []),
                    ...($input['link'] ? ['aria-labelledby' => $input['id']] : [])
                ]
            ])
                @slot('before')
                    @if (!empty($input['image']))
                        @image($input['image'])
                        @endimage
                    @endif
                @endslot
                @group([
                    'direction' => 'vertical'
                ])
                    @group([
                        'justifyContent' => 'space-between'
                    ])
                        @typography([
                            'element' => 'h2',
                            'variant' => 'h3',
                            'id'      => $input['id']
                        ])
                            {{ $input['title'] }}
                        @endtypography
                        @if (!empty($input['icon']))
                            @element([
                                'classList' => [
                                    'u-display--flex',
                                    'u-detail-shadow-3'
                                ]
                            ])
                                @icon([
                                    'icon' => $input['icon'],
                                    'size' => 'md',
                                    'color' => 'black'
                                ])
                                @endicon
                            @endelement
                        @endif
                    @endgroup
                    @if(!empty($input['content']))
                        @typography([])
                            {!! $input['content'] !!}
                        @endtypography
                    @endif
                @endgroup
            @endcollection__item        
        @endforeach
    @endif
@endcollection