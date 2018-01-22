@if (isset($menu) && is_array($menu) && !empty($menu))
@extends('widget.header-widget')
    @section('widget')
        <nav>
            @include('components.navbar', array('links' => $menu))
        </nav>
    @stop
@endif
