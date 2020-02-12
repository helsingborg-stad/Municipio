<!-- typography.blade.php -->
<{{ $element }} id="{{ $id }}" class="{{ $class }}" {!! $attribute !!}>
    @notice([
        'type' => $type,
        'message' => $message,
        'icon' => $icon
    ])
    @endnotice
</{{ $element }}>