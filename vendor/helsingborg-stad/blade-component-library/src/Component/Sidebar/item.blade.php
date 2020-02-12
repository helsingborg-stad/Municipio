@if ($top_level)
    <div class="c-sidebar__subcontainer--top">
@else
    <div class="c-sidebar__subcontainer"
        js-toggle-item="{{$appendID}}"
        js-toggle-class="c-sidebar__item--is-expanded">
@endif

    @foreach ($items as $item)
        <div class="{{$baseClass}}__item">
        
            <a class="{{$baseClass}}__link" href="{{$item['href']}}" aria-label="{{$item['label']}}" item-active="{{isset($item['active']) ? "true" : "false"}}">
                {{$item['label']}}
            </a>

            @if (isset($item['children']))
                @php
                    $rndId = uniqid();
                @endphp
                <div class="{{$baseClass}}__toggle"
                    js-toggle-trigger="{{$rndId}}"
                    aria-pressed="false">

                    <div class="bar"></div>
                    <div class="bar"></div>
                </div>

                @include ('Sidebar.item', array('items' => $item['children'], 'appendID' => $rndId, 'top_level' => false))
            @endif
        </div>
    @endforeach
</div>