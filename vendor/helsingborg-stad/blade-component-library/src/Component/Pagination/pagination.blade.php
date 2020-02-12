<!-- pagination.blade.php -->
@if($list)
<{{$componentElement}} id="{{ $id }}" class="{{ $class }}" role="navigation" aria-label="Pagination Navigation" {!! $attribute !!}>
    <{{$listElement}} class="{{$baseClass}}__list">

        @if($previous)
          <{{$listItem}} class="{{$baseClass}}__item {{$baseClass}}__item--previous">
            <a class="{{$baseClass}}__link" href="{{ $previous }}" aria-label="Previous page">
              <span class="{{$baseClass}}__label">
                @icon(['icon' => 'chevron_left'])
                @endicon
              </span>
            </a>
          </{{$listItem}}>
        @endif

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


        @if($next)
          <{{$listItem}} class="{{$baseClass}}__item {{$baseClass}}__item--next">
            <a class="{{$baseClass}}__link" href="{{ $next }}" aria-label="Next page">
              <span class="{{$baseClass}}__label">
                @icon(['icon' => 'chevron_right'])
                @endicon
              </span>
            </a>
          </{{$listItem}}>
        @endif

    </{{$listElement}}>
</{{$componentElement}}>
@else
<!-- No pagination data -->
@endif