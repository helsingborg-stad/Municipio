<!-- alert.blade.php -->
<div id="{{ $id }}" class="{{ $class }}" {!! $attribute !!}>

    <div class="{{$baseClass}}__inner">

        @if($title) 
            <h2 class="{{$baseClass}}__heading">
                {{$title}}
            </h2>
        @endif

        @if($slot)
            <div class="{{$baseClass}}__content">
                {{$slot}}
            </div>
        @endif

        @if($dismiss) 
            @button(['href' => "#dismiss", 'isPrimary' => false])
                {{$dismiss}}
            @endbutton
        @endif

        @if($accept)
            @button(['href' => "#accept", 'isPrimary' => true])
                {{$accept}}
            @endbutton
        @endif

    </div>

</div>