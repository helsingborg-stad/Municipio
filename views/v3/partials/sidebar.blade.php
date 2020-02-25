@if(isset($id) && $id)
    @if (is_active_sidebar($id))
        <div class="sidebar-{{$id}} {{isset($classes) ? is_array($classes) ? implode(' ', $classes) : $classes : ''}}">
            @php dynamic_sidebar($id); @endphp
        </div>
    @endif
@endif