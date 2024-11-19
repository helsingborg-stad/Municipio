@if($gridColumnClass)
    <div class="{{ $gridColumnClass }}">
@endif

    @include('BoxItem')

@if($gridColumnClass)
    </div>
@endif
