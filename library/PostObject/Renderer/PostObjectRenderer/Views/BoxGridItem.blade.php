@if($config['gridColumnClass'])
    <div class="{{ $config['gridColumnClass'] }}">
@endif

    @include('BoxItem')

@if($config['gridColumnClass'])
    </div>
@endif
