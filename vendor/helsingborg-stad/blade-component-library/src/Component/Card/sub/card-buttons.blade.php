@foreach ($buttons as $indexKey => $button)
    @button([
        'href' => $button["href"],
        'text' => $button["text"],
        'color' => $button["color"],
        'attributeList' => ['tabindex' => $indexKey],
        'classList' => [$baseClass."__button"],
        'type' => $button['type']
    ])
    @endbutton
@endforeach