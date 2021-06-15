@if($tabMenuItems)
    <nav role="navigation" aria-label="{{$lang->relatedLinks}}"
        class="u-display--flex@lg u-display--flex@lx u-display--none@xs
                u-display--none@sm u-display--none@md u-print-display--none">
        @group([
            'classList' => [
                'u-justify-content--center@xs', 
                'u-justify-content--center@sm', 
                'u-justify-content--end', 
                'u-box-shadow--1',
                'u-rounded',
                'u-margin--auto'
            ]
        ])
            @foreach($tabMenuItems as $item)
                @button([
                    'href'  => $item['href'], 
                    'text'  => $item['label'],
                    'size'  => 'sm',
                    'style' => 'basic',
                    'attributeList' => [
                        'role' => 'menuitem'
                    ]
                ])
                @endbutton
            @endforeach
        @endgroup
    </nav>
@endif