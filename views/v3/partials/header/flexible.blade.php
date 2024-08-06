@extends('templates.header', ['classList' => ['c-header']])
    <style>
  .grid-container {
    display: grid;
    grid-template-columns: 1fr 4fr;
    grid-template-rows: 1fr 1fr;
}

.item1 {
    grid-row: span 2;
    display: flex;
    justify-content: center;
    align-items: center;
}

.item2::after,
.item3::after {
    content: "";
    position: absolute;
    width: 300vw;
    left: 0;
    right: 0;
    transform: translateX(-50%);
    height: 100%;
    z-index: -1;
}

.item2::after {
    background-color: white;
}
.item3::after {
    background-color: pink;
}

.item2, .item3 {
    display: flex;
    justify-content: right;
    align-items: center;
    position: relative;
}
    </style>

@if (!empty($headerData))
  <div class="o-container grid-container">
        <div class="item1">
            @if(!empty($headerData['logotypeItems']))
                @foreach($headerData['logotypeItems'] as $item)
                    @includeIf('partials.header.components.' . $item)
                @endforeach
            @endif
        </div>
        <div class="item2">            
            @if(!empty($headerData['mainUpperItems']))
                @foreach($headerData['mainUpperItems'] as $item)
                    @includeIf('partials.header.components.' . $item)
                @endforeach
            @endif</div>
        <div class="item3">
            @if(!empty($headerData['mainLowerItems']))
                @foreach($headerData['mainLowerItems'] as $item)
                    @includeIf('partials.header.components.' . $item)
                @endforeach
            @endif
        </div>
    </div>
    
    @if(!empty($megaMenuItems) && (in_array('mega-menu', $headerData['mainLowerItems']) || in_array('mega-menu', $headerData['mainUpperItems'])))
        @include('partials.navigation.megamenu')
    @endif
    @if(in_array('search-modal', $headerData['mainLowerItems']) || in_array('search-modal', $headerData['mainUpperItems']))
        @include('partials.search.search-modal')
    @endif
@endif
