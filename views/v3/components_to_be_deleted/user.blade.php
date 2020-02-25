@if(empty($link))
{{--*/ $blocktype = 'div' /*--}}
@else
{{--*/ $blocktype = 'a' /*--}}
@endif

<{{$blocktype}} href="{{$link}}" class="user user--horizontal">

    @includeIf('utilities.image', ['src' => $src, 'class' => 'u-rounded-circle'])

    <div class="details">

        @if($name)
        <span class="name">{{$name}}</span>
        @endif

        @if($role)
        <span class="role">{{$role}}</span>
        @endif

        {{ $details }}

    </div>

    {{ $slot }}
</{{$blocktype}}>
