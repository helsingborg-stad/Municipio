<!-- avatar.blade.php -->
<div id="{{ $id }}" class="{{ $class }}" {!! $attribute !!}>
    @if($image)
        <img src="{{ $image }}" class="{{$baseClass}}__image" alt="{{ $label }}" aria-label="{{ $label }}"/>
    @endif

    @if($icon)
        <span class="{{$baseClass}}__icon" aria-label="{{ $label }}">
            @icon(
                [
                    'icon' => $icon['name'],
                    'classList' => ["c-icon--size-".$icon['size']]
                ]
            )
            @endicon
        </span>
    @endif

    @if($initials)
        <svg class="{{$baseClass}}__initials" aria-label="{{ $label }}" viewBox="0 0 1000 1000" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
            <text font-size="380" y="50%" x="50%" fill="#fff" text-anchor="middle" alignment-baseline="central">{{$initials}}</text>
        </svg>
    @endif
</div>