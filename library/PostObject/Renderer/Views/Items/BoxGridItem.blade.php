@if($gridColumnClass)
    <div class="{{ $gridColumnClass }}">
@endif

    @include('Items.BoxItem')

@if($gridColumnClass)
    </div>
@endif
