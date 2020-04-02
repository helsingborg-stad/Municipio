<!-- pagination.blade.php -->
@if($list)
<{{$componentElement}} id="{{ $id }}" class="{{ $class }}" role="navigation" aria-label="Pagination Navigation" {!! $attribute !!}>
    <{{$listElement}} class="{{$baseClass}}__list">
        @button([
          'style' => 'basic',
          'color' => 'primary',
          'icon' => 'chevron_left',
          'attributeList' => ['disabled' => $previousDisabled],
          'href' => $previous,
          'classList' => [$baseClass . '__item--previous', $baseClass . '__item']
        ])
        @endbutton

        @foreach($list as $item) 
          @if($loop->index+1 == $current)
          <{{$listItem}} class="{{$baseClass}}__item {{$baseClass}}__item{{ $currentClass }}">
            <a class="{{$baseClass}}__link" href="{{ $item['href'] }}" aria-label="{{ $item['label'] }}" aria-current="true">
              <span class="{{$baseClass}}__label">
                {{ $loop->index+1 }}
              </span>
            </a>
          </{{$listItem}}>
          @else
          <{{$listItem}} class="{{$baseClass}}__item">
            <a class="{{$baseClass}}__link" href="{{ $item['href'] }}" aria-label="{{ $item['label'] }}">
              <span class="{{$baseClass}}__label">
                {{ $loop->index+1 }}
              </span>
            </a>
          </{{$listItem}}>
          @endif
        @endforeach

        @button([
          'style' => 'basic',
          'color' => 'primary',
          'icon' => 'chevron_right',
          'attributeList' => ['disabled' => $nextDisabled],
          'href' => $next,
          'classList' => [$baseClass . '__item--next', $baseClass . '__item']
        ])
        @endbutton

    </{{$listElement}}>
</{{$componentElement}}>
@else
<!-- No pagination data -->
@endif