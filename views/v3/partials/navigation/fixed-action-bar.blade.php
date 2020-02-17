<div class="fab {{ $fab['classes'] }}">
    <div class="c-dropdown js-dropdown">
        @fab([
            'position' => 'bottom-left',
            'spacing' => 'md',
            classList => ['c-dropdown__toggle', 'js-dropdown__toggle',
        'c-dropdown__toggle--rotate-plus']
        btn btn-floating ',
            'button' => [
                'href' => '#btn-3',
                'background' => 'primary',
                'isIconButton' => true,
                'icon' => [
                    'name' => 'add_box',
                    'color' => 'white',
                    'size' => 'lg'
                ],
        classList => '',
                'reverseIcon' => true,
                'size' => 'lg',
                'color' => 'secondary',
                'floating' => [
                    'animate' => false,
                    'hover' => true
                ],
            ]
            ])
        @endfab

      <div class="c-dropdown__menu c-dropdown__menu--zoom-in c-dropdown__menu--up c-dropdown__menu--right c-dropdown__menu--bubble">
        {!! $fab['menu'] !!}
      </div>
    </div>
</div>
