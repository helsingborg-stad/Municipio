@if(isset($verticalNav) && is_array($verticalNav) && !empty($verticalNav) && count($verticalNav) > 1)
    <ul class="scroll-dots">
        @foreach($verticalNav as $navItem)
            <li><a href="{!! $navItem['link'] !!}" data-link-tooltip="{{ $navItem['title'] }}"></a></li>
        @endforeach
    </ul>
@endif
