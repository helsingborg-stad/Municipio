<a href="#userid" class="user">
    <div class="image"></div>
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
</a>
