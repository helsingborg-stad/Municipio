@if (has_tag())
<div class="post-tags-wrapper">
    <?php _e('Tags', 'municipio'); ?>:
    <ul class="tags tags-white tags">
        {{ the_tags('<li>', '</li><li>', '</li>') }}
    </ul>
</div>
@endif
