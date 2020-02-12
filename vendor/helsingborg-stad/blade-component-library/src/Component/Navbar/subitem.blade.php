<div class="c-navbar__subitem" js-menu-target="{{$targetId}}" data-append-submenu="{{$appendID}}" >
    @if (is_array($item))
        @foreach ($item as $childItem)
            <div class="c-navbar__item">
                
                <a href="{{$childItem['href']}}" aria-label="{{$childItem['name']}}">{{$childItem['name']}}</a>
            
                @if (isset($childItem['list']))
                    <div class="c-navbar__toggle">
                        @button([
                            'isIconButton' =>  true,
                            'icon' => ['name' => 'expand_more', 'color' => 'primary', 'size' => 'md'],
                            'href' => 'javascript:void(0)',
                            'background' => false,
                            'attributeList' => [
                                'js-menu-trigger' => 'c-navbar__subitem--expanded',
                                'js-menu-dart' => $loop->iteration,
                                'data-load-subitem' => $childItem['id']
                            ]
                        ])
                        @endbutton
                    </div>
                    @include('Navbar.subitem', array('item' => $childItem['list'], 'appendID'=> $childItem['id'], 'targetId' => $loop->iteration))
                @endif
            </div> 
        @endforeach
    @endif
</div>