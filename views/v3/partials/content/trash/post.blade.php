@php
    $url = wp_attachment_is_image($post->ID) ? wp_get_attachment_url($post->ID) : false;
@endphp

@element([
    'classList' => [
        'municipio-trash-page__post-wrapper'
    ]
])
    @element([
        'classList' => [
            'municipio-trash-page__post'
        ]
    ])
        @if(wp_attachment_is_image($post->ID))
            @image([
                'src' => wp_get_attachment_url($post->ID),
                'classList' => [
                    'municipio-trash-page__post-image'
                ],
            ])
            @endimage
        @endif

        @include('partials.content.trash.actions', [
            'post' => $post
        ])
    @endelement
    @typography([
        'attributeList' => [
            'style' => 'margin: 0;'
        ]
    ])
        {{ $post->post_title }}
    @endtypography
@endelement