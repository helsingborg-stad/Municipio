<footer class="page-footer grid-table grid-va-middle">
    <div class="grid-md-5">
        @include('partials.timestamps')
    </div>

    <div class="grid-md-5 {{ get_field('show_share', get_the_id()) == 'false' ? 'text-right' : '' }}">
        <a href="{{ comments_link() }}">Kommentarer ({{ comments_number('0', '1', '%') }})</a>
    </div>

    @if (get_field('show_share', get_the_id()) != 'false' && get_field('show_share_master', 'option') != 'false')
    <div class="grid-md-2 text-right">
        @include('partials.social-share')
    </div>
    @endif
</footer>
