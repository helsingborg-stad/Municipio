@if($tabMenuItems)
    <nav aria-label="{{$lang->relatedLinks}}"
        class="u-display--flex@lg u-display--flex@lx u-display--none@xs
                u-display--none@sm u-display--none@md u-print-display--none">
        @group([
            'classList' => [
                'u-justify-content--center@xs', 
                'u-justify-content--center@sm', 
                'u-justify-content--end', 
                'u-box-shadow--1',
                'u-rounded',
                'u-margin--auto',
                'u-color__bg--lightest'
            ]
        ])
            @foreach($tabMenuItems as $item)
                @button([
                    'href'  => $item['href'], 
                    'text'  => $item['label'],
                    'size'  => 'sm',
                    'style' => 'basic',
                    'icon' => $item['icon']['icon'] ?? null,
                    'attributeList' => [
                        'role' => 'menuitem'
                    ],
                    'reversePositions' => true,
                    'classList' => [
                        'u-padding__x--2'
                    ]
                ])
                @endbutton
            @endforeach
        @endgroup
    </nav>
@endif