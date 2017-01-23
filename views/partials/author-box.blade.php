@if (is_author())
<div class="box box-filled box-author">
    <img src="{{ get_the_author_meta('user_profile_picture') }}" alt="{{ get_the_author_meta('nicename') }}" class="box-image">

    <div class="box-content">
        <div class="author-name">{{ municipio_get_author_full_name() }}</div>
        <div class="author-description">{{ get_the_author_meta('description') }}</div>
    </div>
</div>
@endif
