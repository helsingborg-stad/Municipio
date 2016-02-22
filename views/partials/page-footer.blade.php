<footer class="page-footer grid grid-table no-padding">
    <div class="grid-md-6">
        @include('partials.timestamps')
    </div>

    <div class="grid-md-6 text-right">
        {{ var_dump(get_field('show_share', get_the_id())) }}
        @if (get_field('show_share', get_the_id()) !== false && get_field('show_share_master', 'option') !== false)
            @include('partials.social-share')
        @endif
    </div>
</footer>
