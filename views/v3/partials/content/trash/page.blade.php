@element([
    'classList' => [
        'municipio-trash-page'
    ]
])
    @typography([
        'element' => 'h1',
        'attributeList' => [
            'style' => 'margin-bottom: 2rem;'
        ]
    ])
        {{ $lang['trashedMedia'] }}
    @endtypography
    @element([
        'classList' => [
            'municipio-trash-page__posts'
        ]
    ])
    @foreach($posts as $post)
        @include('partials.content.trash.post', [
            'post' => $post
        ])
    @endforeach
    @endelement
@endelement