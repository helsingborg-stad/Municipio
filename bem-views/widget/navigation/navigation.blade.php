{!! $args['before_widget'] !!}
    <nav {!! $attributes !!}>
        @foreach ($navItems as $navItem)
            @if (isset($navItem['template']) && $navItem['template'])
                @include($navItem['template'])
            @endif
        @endforeach
    </nav>
{!! $args['after_widget'] !!}

