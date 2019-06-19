<span class="dropdown">
<span class="btn btn-primary dropdown-toggle hidden"><?php _e('More', 'municipio'); ?></span>
<ul class="dropdown-menu nav-grouped-overflow hidden"></ul>
</span>

@if (get_field('header_dropdown_links', 'option') === true && \Municipio\Helper\Navigation::getMenuNameByLocation('dropdown-links-menu'))
    <span class="c-dropdown js-dropdown">
        <button class="c-dropdown__toggle js-dropdown__toggle btn btn-primary">{{ \Municipio\Helper\Navigation::getMenuNameByLocation('dropdown-links-menu')}}</button>
        <span class="c-dropdown__menu c-dropdown__menu--bubble">
            {!! \Municipio\Theme\Navigation::outputDropdownLinksMenu() !!}
        </span>
    </span>
@endif