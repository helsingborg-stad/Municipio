@collection([
    'unbox' => true,
    'classList' => ['o-grid', 'o-grid--horizontal']
])
    @foreach($postObjects as $postObject)
        @include('Items.CollectionItem', ['postObject' => $postObject])
    @endforeach
@endcollection