@card([ 'heading' => false ])
    @if (!empty($title))
        <div class="c-card__header">
            @include('Items.Partials.Title', ['variant' => 'h4', 'classList' => [], 'title' => $title])
        </div>
    @endif
    <div class="o-grid{{ !empty($stretch) ? ' o-grid--stretch' : '' }}">
        <div class="o-grid-12">
            @collection([ 'sharpTop' => true, 'bordered' => true ])  
                @foreach ($postObjects as $postObject)
                    @include('Items.ListItem', ['postObject' => $postObject])
                @endforeach
            @endcollection
        </div>
    </div>
@endcard