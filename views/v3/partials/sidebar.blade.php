@if(isset($id) && $id)
    @php do_action('Municipio/sidebar/beforeSidebar', $id); @endphp
    @section('sidebar.' . $id . '.before')@show
    @if (is_active_sidebar($id))
        <div id="sidebar-{{$id}}" class="sidebar-{{$id}} {{isset($classes) ? is_array($classes) ? implode(' ', $classes) : $classes : ''}}">
            @php dynamic_sidebar($id); @endphp {{-- TODO: Move functions to Controller --}}
        </div>
    @endif
    @section('sidebar.' . $id . '.after')@show
    @php do_action('Municipio/sidebar/afterSidebar', $id); @endphp
@endif