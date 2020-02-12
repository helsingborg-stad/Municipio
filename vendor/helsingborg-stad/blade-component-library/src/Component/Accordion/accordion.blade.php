<!-- accordion.blade.php -->
@if($list)
<{{$componentElement}} id="{{ $id }}" class="{{ $class }}" js-expand-container {!! $attribute !!}>
    @foreach($list as $section)
      <{{$sectionElement}} class="{{$baseClass}}__section">

        <{{$sectionHeadingElement}} class="{{$baseClass}}__button" aria-controls="{{ $baseClass }}__aria-{{ $id }}-{{ $loop->index }}" aria-expanded="false" js-expand-button>
            <span class="{{$baseClass}}__button-wrapper" tabindex="-1">
                {!!$beforeHeading!!}
                {{ $section['heading'] }}
                {!!$afterHeading!!}

                @icon(['icon' => 'keyboard_arrow_down', 'size' => 'md', 'classList' => [$baseClass . '__icon']])
                @endicon
            </span>
        </{{$sectionHeadingElement}}>

        <{{$sectionContentElement}} class="{{$baseClass}}__content" id="{{ $baseClass }}__aria-{{ $id }}-{{ $loop->index }}" aria-hidden="true">
          {!!$beforeContent!!}
          {{ $section['content'] }}
          {!!$afterContent!!}
        </{{$sectionContentElement}}>

      </{{$sectionElement}}>
    @endforeach
  </{{$componentElement}}>
@else
  <!-- No accordion list data -->
@endif
