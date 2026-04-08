@if(isset($id) && $id)
    @section('sidebar.' . $id . '.before')@show
    @if (is_active_sidebar($id))
        @section('sidebar.active.' . $id . '.before')@show
        @scope([
            'id' => 'sidebar-' . $id, 
            'name' => ['sidebar', 'sidebar-' . $id, $postType . '-sidebar-' . $id],
            'classList' => $classes ?? []
        ])
            @php dynamic_sidebar($id); @endphp
        @endscope
        @section('sidebar.active.' . $id . '.after')@show
    @else
        @section('sidebar.inactive.' . $id)@show
    @endif
    @section('sidebar.' . $id . '.after')@show
@endif