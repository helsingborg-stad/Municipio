@if($href)
    <a class="{{$baseClass}}__link {{$baseClass}}__image-link" href="{{$href}}">
        @image([
            'src'=> $image,
            'alt' => $alt,
            'classList' => [$baseClass."__image"]
        ])
        @endimage
    </a>
@else
    @image([
        'src'=> $image,
        'alt' => $alt,
        'classList' => [$baseClass."__image"]
    ])
    @endimage
@endif