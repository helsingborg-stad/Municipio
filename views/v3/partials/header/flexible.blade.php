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

@dump(get_theme_mod('header_sortable_section_main_lower'))
  <div class="o-container grid-container">
        <div class="item1">Logo</div>
        <div class="item2">            
            <?php
                $upper = get_theme_mod('header_sortable_section_main_upper');
            ?>
            @if($upper)
                @foreach($upper as $menu)
                    @includeIf('partials.header.components.' . $menu)
                @endforeach
            @endif</div>
        <div class="item3">
            <?php
                $lower = get_theme_mod('header_sortable_section_main_lower');
            ?>
            @if($lower)
                @foreach($lower as $menu)
                    @includeIf('partials.header.components.' . $menu)
                @endforeach
            @endif
        </div>
    </div>
    @if(in_array('mega-menu', $lower) || in_array('mega-menu', $upper))
        @include('partials.navigation.megamenu')
    @endif
    @if(in_array('search-modal', $lower) || in_array('search-modal', $upper))
        @include('partials.search.search-modal')
    @endif
