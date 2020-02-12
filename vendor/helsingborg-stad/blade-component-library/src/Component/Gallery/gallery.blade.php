<!-- gallery.blade.php -->
@if($list)
    @php
        $uniqueId = "gallery_".\BladeComponentLibrary\Component\Gallery\Gallery::getUnique();
    @endphp

    <ul id="{{ $id }}" class="{{ $class }}" {!! $attribute !!}>
        @foreach($list as $key => $item)
            <li class="{{$baseClass}}__item {{$baseClass}}__item-{{ $loop->index }}">

                @image([
                    'src'=> $item['smallImage'],
                    'alt' => $item['alt'],
                    'caption' => $item['caption'],
                    'fullWidth' => true,
                    'attributeList' => [
                        'data-open' => $uniqueId,
                        'data-large-img' =>  $item['largeImage'],
                        'data-stepping' => $loop->index,
                        'data-caption' => $item['caption']
                        ]
                ])
                @endimage

            </li>
        @endforeach
    </ul>

    @modal([
        'isPanel' => false,
        'overlay' => 'dark',
        'animation' => 'scale-up',
        'navigation' => true,
        'id' => $uniqueId,
        'classList' => [
            'c-modal--gallery',
            'c-modal--stepper'
        ]
    ])

    @image([
        'src'=> '',
        'caption'=> $item['caption']
    ])
    @endimage

    @endmodal
@else
    <!-- No gallery data -->
@endif