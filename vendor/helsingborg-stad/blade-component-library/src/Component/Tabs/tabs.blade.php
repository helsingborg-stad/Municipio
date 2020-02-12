<!-- tabs.blade.php -->
@if($tabs)
    <{{$componentElement}} id="{{ $id }}" class="{{ $class }}" role="tablist" js-expand-container {!! $attribute !!}>
        <div class="{{ $baseClass }}__header">
            @foreach($tabs as $tab)
                <{{$headingElement}} role="tab" class="{{$baseClass}}__button" aria-controls="{{ $baseClass }}__aria-{{ $id }}-{{ $loop->index }}" aria-expanded="{{ $loop->index === 0 ? 'true' : 'false' }}" js-expand-button>
                    <span class="{{$baseClass}}__button-wrapper" tabindex="-1">
                        {{ $tab['title'] ?? '' }}
                    </span>
                </{{$headingElement}}>
            @endforeach
        </div>
        @foreach($tabs as $tab)
            <{{$contentElement}} class="{{$baseClass}}__content" role="tabpanel" id="{{ $baseClass }}__aria-{{ $id }}-{{ $loop->index }}" aria-hidden="{{ $loop->index === 0 ? 'false' : 'true' }}">
                {!! $tab['content'] ?? '' !!}
            </{{$contentElement}}>
        @endforeach
    </{{$componentElement}}>
@else
  <!-- No tabs data -->
@endif
