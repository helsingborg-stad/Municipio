@if(is_array($verticalNav) && !empty($verticalNav))
    <ul class="scroll-dots">
        @foreach($verticalNav as $navItem)
            <li><a href="{!! $navItem['link'] !!}" data-link-tooltip="{{ $navItem['title'] }}"></a></li>
        @endforeach
    </ul>
@endif
