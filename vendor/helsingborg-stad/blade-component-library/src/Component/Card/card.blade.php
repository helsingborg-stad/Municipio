<!-- card.blade.php -->
<div id="{{ $id }}" class="{{$class}}" {!! $attribute !!}>
    @include('Card.sub.head')
    @include('Card.sub.body')
    @include('Card.sub.footer')
</div>