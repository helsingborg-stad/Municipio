<div class="post-categories-wrapper">
    <?php _e('Categories', 'municipio'); ?>:
    @if (has_category())
    {{ the_category() }}
    @else
    <?php _e('No categories', 'municipio'); ?>
    @endif
</div>
