<!-- fileinput.blade.php -->

<div id="{{ $id }}" class="{{ $class }}" {!! $attribute !!}>
<input type="file" class="{{ $baseClass }}__input" name="{{ $multiple ? $name . '[]' : $name }}" id="fs_{{ $id }}" accept="{{ $accept }}" {{ $multiple ? 'multiple' : '' }} />
    <label for="fs_{{ $id }}" class="{{ $baseClass }}__label">

    @if($icon)

            @icon([
                'icon' => 'file_upload',
                'size' => 'md',
                'color' => 'white'
            ])
            @endicon
     @endif
            {{ $beforeLabel }}
                {{ $label }}
            {{ $afterLabel }}

    </label>

    @listing([
        'list' => [
            ['label' => ''],
        ],
        'elementType' => 'ul',
        'id' => $id.'_fileContainer'

    ])
    @endlisting
</div>
