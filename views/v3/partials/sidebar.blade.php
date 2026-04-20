@if(isset($id) && $id)
    @section('sidebar.' . $id . '.before')@show
    @if (is_active_sidebar($id))
        
        @php
            ob_start();
            dynamic_sidebar($id);
            $sidebarMarkup = trim((string) ob_get_clean());
        @endphp

        @if(!empty($sidebarMarkup))

            @section('sidebar.active.' . $id . '.before')@show

            @scope(['name' => ['sidebar', 'sidebar-' . $id, $postType . '-sidebar-' . $id],])
                @element([ 'id' => 'sidebar-' . $id, 'classList' => $classes ?? [], ])
                    {!! $sidebarMarkup !!}
                @endelement
            @endscope

            @section('sidebar.active.' . $id . '.after')@show

        @endif
        
    @else
        @section('sidebar.inactive.' . $id)@show
    @endif
    @section('sidebar.' . $id . '.after')@show
@endif