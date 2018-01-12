@if (isset($menu) && is_array($menu) && !empty($menu))
    <nav>
        @include('components.navbar', array('links' => $menu))
    </nav>
@endif
