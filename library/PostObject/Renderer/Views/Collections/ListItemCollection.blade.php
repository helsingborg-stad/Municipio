@card([
    'heading' => false
])
    <div class="o-grid{{ !empty($stretch) ? ' o-grid--stretch' : '' }}">
        <div class="o-grid-12">
            @collection([
                'sharpTop' => true,
                'bordered' => true
            ])
                @foreach ($postObjects as $postObject)
                    @include('Items.ListItem', [ 'postObject' => $postObject ])
                @endforeach
            @endcollection
        </div>
    </div>
@endcard