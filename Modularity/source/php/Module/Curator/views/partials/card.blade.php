@foreach ($posts as $post)
    <div class="open-modal modularity-socialmedia__item {{ $columnClasses }}">
        @card([
            'image' => $post->image ?: false,
            'ratio' => '4:3',
            'heading' => !empty($post->title) ? $post->title : '@' . $post->user_screen_name,
            'subHeading' => !empty($post->title) ? '@' . $post->user_screen_name : false,
            'date' => !empty($post->raw_date) ? $post->raw_date : false,
            'metaFirst' => true,
            'content' => $post->text,
            'classList' => ['u-height--100'],
            'attributeList' => ['data-open' => 'modal-' . $post->id]
        ])
        @endcard

    </div>
    @include('partials.modal', ['post' => $post])
@endforeach
