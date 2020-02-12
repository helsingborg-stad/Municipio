<!-- sidebar.blade.php -->
<nav id="{{ $id }}" class="{{$class}} c-sidebar--collapsed" {!! $attribute !!} js-sidebar>

    @if (!empty($logo))
        <a href="/">
            <div class="{{$baseClass}}__logo">
                @image([
                    'src'=> $logo,
                    'alt' => "A logo"
                    ])
                @endimage
            </div>
        </a>
    @endif

    @if ($showHideButton)
        <div class="u-display--none@md u-display--none@lg u-display--none@xl">
            <a class="{{$baseClass}}__hide">
                @icon(['icon' => 'arrow_back', 'size' => 'lg', 'color' => 'black'])
                @endicon

                Hide
            </a>
        </div>
    @endif

    @include ('Sidebar.item', array('items' => $items, 'appendID' => uniqid(), 'top_level' => true))
</nav>

<div class="{{$baseClass}}__backdrop" js-sidebar-trigger>
</div>