<!-- footer.blade.php -->
<{{$componentElement}} id="{{ $id }}" class="{{ $class }}" {!! $attribute !!}>
  <div class="g-divider g-divider--lg"></div>
  <div class="{{ $baseClass }}__body">
      <a href="{{$logotypeHref}}" class="{{ $baseClass }}__home-link">
        {{$logotype}}
      </a>
    <div class="{{$baseClass}}__nav">
      {{$about}}
      {{$documentation}}
      {{$links}}
      </div>
  </div>
</{{$componentElement}}