@if(isset($id) && $id)
    @if (is_active_sidebar($id))
        <div class="grid grid--columns s-dynamic-sidebar-{{$id}}">
            <?php dynamic_sidebar($id); ?>
        </div>
    @endif
@endif
