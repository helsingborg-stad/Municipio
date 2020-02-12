<!-- video.blade.php -->
@if($formats)
    <video id="{{ $id }}" class="{{ $class }}" width="{{$width}}" height="{{$height}}" {{$controls}} {{$muted}} {{$autoplay}} {!! $attribute !!}>

        @foreach($formats as $format)
            <source class="{{ $baseClass }}__source" src="{{$format['src']}}" type="video/{{$format['type']}}">
        @endforeach

        @if($errorMessage)
            @notice(['isWarning' => true])
            {{$errorMessage}}
            @endnotice
        @endif
            
    </video>
@else 
<!-- No video data defined -->
@endif