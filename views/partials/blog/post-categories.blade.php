@if (has_category())
<div class="post-categories-wrapper">
    <?php _e('Categories', 'municipio'); ?>:
    {{ the_category() }}
</div>
@endif
