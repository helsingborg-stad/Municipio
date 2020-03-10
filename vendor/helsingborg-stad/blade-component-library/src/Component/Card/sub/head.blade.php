@if($dropdown)
    <div class="{{$baseClass}}__dropdown {{$baseClass}}__dropdown--{{$dropdown['buttonColor']}}">
        @dropdown([
            'items' => $dropdown['items'] ,
            'direction' => 'right',
            'popup' => 'click'
            ])
            @button([
                'icon' => 'more_vert',
                'size' => 'lg',
                'type' => 'basic'
            ])
            @endbutton
        @enddropdown
    </div>
@endif
@if($image)
<div class="{{$baseClass}}__image {{$baseClass}}__image--{{$image['backgroundColor']}}">
    <div class="{{$baseClass}}__image-background {{$paddedImage}}" style="background-image:url('{{$image['src']}}');"></div>
    </div>
@endif

<div class="{{$baseClass}}__title">
    <div class="{{$baseClass}}__title-headings">
        @typography(['element' => 'h2', 'variant' => 'h2'])
            {{$heading}}
        @endtypography
        
        @typography(['element' => 'h4', 'variant' => 'h4'])
            {{$subHeading}}
        @endtypography
    </div>
    @if($collapsible)
        @button([
        'type' => 'basic',
        'size' => 'md',
        "attributeList" => ['js-toggle-trigger' => $id],
        'icon' => 'expand_more',
        'classList' => ['c-card__title-expand-button']
        ])
        @endbutton
    @endif
</div>