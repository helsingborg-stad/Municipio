<!-- hero.blade.php -->
@if($componentShow)
    <{{$componentElement}} id="{{ $id }}" class="{{ $class }}" {!! $attribute !!}>

        <div class="{{$baseClass}}__body">

            <div class="{{ $baseClass }}__content">

                @if($headline)
                <{{$headingElement}} class="{{$baseClass}}__headline">{{$headline}}</{{$headingElement}}>
                @endif

                @if($byline)
                <{{$bylineElement}} class="{{$baseClass}}__byline">{{$byline}}</{{$bylineElement}}>
                @endif

                @if($content)
                <{{$contentElement}} class="{{$baseClass}}__paragraph">{!! $content !!}</{{$contentElement}}>
                @endif
            
            </div>

            @if($complementaryImage != "")
                <div class="{{ $baseClass }}__complementary-image u-display-none-xs u-display-none-sm" style="background-image: url('{!!$complementaryImage!!}');"></div>
            @endif

            <!-- Scoped component styles -->
            <style scoped>
                .{{$baseClass}}:before {
                    background-image: url('{{$brandSymbol}}'); 
                }
                .{{$baseClass}} {
                    background-image: url('{{$backgroundImage}}'); 
                    background-color: {{$backgroundColor}}; 
                    height: {{$height}}vh;
                }
            </style>
        </div>

    </{{$componentElement}}>
@else 
<!-- No hero data defined -->
@endif