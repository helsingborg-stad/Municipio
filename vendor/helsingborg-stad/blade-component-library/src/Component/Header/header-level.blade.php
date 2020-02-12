<!-- Logo -->
@if(in_array('logotype', $levelContent[$currentLevel]))
<a href="{{$logotypeHref}}" class="{{ $baseClass }}__home-link">
    {{$logotype}}
</a>
@endif

<!-- Menu -->
@if(in_array('menu', $levelContent[$currentLevel]))
<div class="{{ $baseClass }}__menu">
    {{$menu}}
</div>
@endif