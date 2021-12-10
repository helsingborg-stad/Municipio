@if(isset($id) && $id)
    @php do_action('Municipio/sidebar/beforeSidebar', $id); @endphp
    @if (is_active_sidebar($id))
        <div id="sidebar-{{$id}}" class="sidebar-{{$id}} {{isset($classes) ? is_array($classes) ? implode(' ', $classes) : $classes : ''}}">
            @php dynamic_sidebar($id); @endphp {{-- TODO: Move functions to Controller --}}
        </div>
    @endif
    @php do_action('Municipio/sidebar/afterSidebar', $id); @endphp
@endif