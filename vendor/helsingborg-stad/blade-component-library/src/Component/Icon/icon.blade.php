<!-- icon.blade.php -->

    <{{$componentElement}} id="{{ $id }}" class="{{ $class }} material-icons" {!! $attribute !!}>
        @if($icon)
            {{$icon}}
        @else 
            {{$slot}}
        @endif
    </{{$componentElement}}>

<!-- No icon defined -->
