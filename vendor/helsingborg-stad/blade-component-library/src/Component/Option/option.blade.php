<!-- option.blade.php -->
<div id="{{ $id }}" class="{{$class}} c-option__{{$type}}">
    <input {!! $attribute !!} type="{{$type}}"
           class="c-option__{{$type}}--hidden-box"
           id="trigger_{{ $id }}"
           placeholder="{{$label}}"
           value="{{$value}}"/>
    <label for="trigger_{{ $id }}" class="c-option__{{$type}}--label">
        <span class="c-option__{{$type}}--label-box"></span>
        <span class="c-option__{{$type}}--label-text">{{$label}}</span>
    </label>
    <div id="error_input_{{ $id }}_message" class="c-option__input-invalid-message">
        @icon([
            'icon' => 'error',
            'size' => 'sm'
        ])
        @endicon
        <span class="errorText"></span>
    </div>
</div>