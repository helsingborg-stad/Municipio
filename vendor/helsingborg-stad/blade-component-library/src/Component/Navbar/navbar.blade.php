<!-- navbar.blade.php -->
<nav id="{{ $id }}" class="{{$class}}" {!! $attribute !!}>
    <div class="{{$baseClass}}__toggle">
        @icon([
            'icon' => 'menu',
            'size' => 'xl',
            'attributeList' => 
                $sidebar ?
                    ['js-sidebar-trigger' => ""] :
                    ['js-menu-trigger' => "{$baseClass}__list--expanded",
                    'js-menu-dart' => "navbar-expand-{$uid}"]
        ])
        @endicon
    </div>

    <a href="/">
        <div class="{{$baseClass}}__logo">
            @image([
                'src'=> $logo,
                'alt' => "A logo"
                ])
            @endimage
        </div>
    </a>

    <div class="{{$baseClass}}__list" js-menu-target="navbar-expand-{{$uid}}">
        @foreach ($items as  $item)

            <a href="{{$item['href']}}" aria-label="{{$item['label']}}">

                <div class="{{$baseClass}}__item" item-active="{{isset($item['active']) ? "true" : "false"}}">
                    
                    <span>{{$item['label']}}</span>

                    @if (isset($item['children']))
                        <div class="{{$baseClass}}__toggle">
                            @button([
                                'isIconButton' =>  true,
                                'icon' => ['name' => 'expand_more', 'color' => 'primary', 'size' => 'md'],
                                'href' => 'javascript:void(0)',
                                'background' => false,
                                'attributeList' => [
                                    'js-menu-trigger' => "{$baseClass}__subitem--expanded",
                                    'js-menu-dart' => $loop->iteration,
                                    'data-load-submenu' => $item['id']
                                ]
                            ])
                            @endbutton
                        </div>

                        <div class="{{$baseClass}}__subcontainer">
                            @include ('Navbar.subitem', array('item' => $item['children'], 'appendID' => $item['id'], 'targetId' => $loop->iteration))
                        </div>
                    @endif
                </div>
            </a>
        @endforeach
    </div>
</nav>