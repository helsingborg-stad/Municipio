<!-- header.blade.php -->
<{{$componentElement}} id="{{ $id }}" class="{{ $class }}" {!! $attribute !!}>

  @if(!empty($levelContent[1]))
  <div class="{{ $baseClass }}__wrapper {{ $baseClass }}__wrapper--level-1">
    <div class="{{ $baseClass }}__body {{ $baseClass }}__body--level-1">
      @include ('Header.header-level', ['currentLevel' => 1])
    </div>
  </div>
  @endif

  @if(!empty($levelContent[2]))
  <div class="{{ $baseClass }}__wrapper {{ $baseClass }}__wrapper--level-2">
    <div class="{{ $baseClass }}__body {{ $baseClass }}__body--level-2">
      @include ('Header.header-level', ['currentLevel' => 2])
    </div>
  </div>
  @endif

  @if(!empty($levelContent[3]))
  <div class="{{ $baseClass }}__wrapper {{ $baseClass }}__wrapper--level-3">
    <div class="{{ $baseClass }}__body {{ $baseClass }}__body--level-3">
      @include ('Header.header-level', ['currentLevel' => 3])
    </div>
  </div>
  @endif
  
</{{$componentElement}}>