<!-- textarea.blade.php -->
<div class="{{$class}}">
    <textarea {!! $attribute !!}
              placeholder="{{$label}}"
              id="{{ $id }}"
              @if($required)
                  required
                  data-required="1"
              @endif
    >{{$value}}</textarea>
    <label class="c-textarea--label">{{$label}}</label>
    <div id="error_{{ $id }}_message" class="c-textarea-invalid-message">
        @icon([
            'icon' => 'error',
            'size' => 'sm'
        ])
        @endicon
        <span class="errorText"></span>
    </div>
</div>