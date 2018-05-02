{!! $args['before_widget'] !!}
    <nav>
        <ul {!! $attributes !!}>
            @foreach ($navItems as $navItem)
                @if (isset($navItem['template']) && $navItem['template'])
                    @include($navItem['template'])
                @endif
            @endforeach
        </ul>
    </nav>
{!! $args['after_widget'] !!}

