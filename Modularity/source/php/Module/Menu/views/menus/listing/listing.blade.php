@if (empty($wrapped))
    @include('menus.listing.components.content')
@else
    @include('menus.listing.components.paper')
@endif