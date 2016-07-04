<div class="accordion-nav clearfix">
    <?php if ($i > 1) : ?>
        <label class="btn btn-md pull-left" data-guide-nav="prev" for="accordion-missing-{{ $i-1 }}"><i class="fa fa-caret-left"></i> <?php _e('Previous', 'modularity-guides'); ?></label>
    <?php endif; ?>

    <?php if (count($missing) > 1 && $i !== count($missing)) : ?>
    <label class="btn btn-md btn-primary pull-right" data-guide-nav="next" for="accordion-missing-{{ $i+1 }}"><?php _e('Next', 'modularity-guides'); ?> <i class="fa fa-caret-right"></i></label>
    <?php endif; ?>

    <?php if ($i === count($missing)) : ?>
    <button type="submit" class="btn btn-primary pull-right"><?php _e('Save and continue', 'municipio-intranet'); ?></button>
    <?php endif; ?>
</div>
