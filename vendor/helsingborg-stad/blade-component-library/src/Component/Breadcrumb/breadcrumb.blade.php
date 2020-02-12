<!-- breadcrumb.blade.php -->
@if($list)
<{{$componentElement}} id="{{ $id }}" class="{{ $class }}" aria-label="{{$label}}" {!! $attribute !!}>
  <{{$listType}} class="{{$baseClass}}__list">
    @foreach($list as $item) 
      <{{$listItemType}} data-level="{{ $loop->depth }}" class="{{$baseClass}}__item {{$baseClass}}__item_{{ $loop->index }} {{$baseClass}}__item_depth-{{ $loop->depth }}">
        
        @if(isset($item['icon']) && !empty($item['icon']))
          @icon(['icon' => $item['icon']])
          @endicon
        @endif
  
        @if($loop->last) 
          <span class="{{$baseClass}}__label" aria-current="page">
            {{ $item['label'] }}
          </span>
        @else 
          <a class="{{$baseClass}}__link" href="{{ $item['href'] }}">
            <span class="{{$baseClass}}__label">
              {{ $item['label'] }}
            </span>
          </a>
        @endif

      </{{$listItemType}}>
    @endforeach
  </{{$listType}}>
</{{$componentElement}}>
@else
<!-- No breadcrumb data -->
@endif