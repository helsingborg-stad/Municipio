@if($type) 
  @paper(['padding' => '3'])

    @if (!$hideTitle && !empty($postTitle))
      @typography([
        'element' => 'h2', 
        'variant' => 'h2', 
        'classList' => ['module-title']
      ])
        {!! $postTitle !!}
      @endtypography
    @endif

    @if($content)
      @typography([
        "element" => "p",
        "classList" => ["u-margin__top--0", "u-margin__bottom--2"]
      ])
        {{ $content }}
      @endtypography
    @endif
      
    @include('service.' . $type)

  @endpaper
@else
  @notice([
    'type' => 'info',
    'message' => [
      'title' => $lang->incomplete->title,
      'text' => $lang->incomplete->text,
    ],
    'icon' => [
        'name' => 'electrical_services'
    ]
  ])
  @endnotice
@endif