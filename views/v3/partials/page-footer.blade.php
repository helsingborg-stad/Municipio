<?php do_action('customer-feedback'); ?>

<footer class="page-footer u-mb-4">
    @if (get_field('post_show_share', get_the_id()) !== false && get_field('page_show_share', 'option') !== false)
    <div class="grid grid--columns">
        <div class="grid-xs-12">
            <div class="box box-border gutter gutter-horizontal no-margin hidden-print">
                <div class="gutter gutter-vertical gutter-sm">
                <div class="grid grid-table grid-va-middle no-margin no-padding">
                    <div class="grid-auto">
                        <i class="pricon pricon-share pricon-lg" style="margin-right:5px;"></i> <strong><?php _e('Share the page', 'municipio'); ?>:</strong> <span class="u-hidden@xs">{{ the_title() }}</span>
                    </div>
                    <div class="grid-fit-content text-right-md text-right-lg">
                        @include('partials.social-share')
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="grid">
        <div class="grid-xs-12">
            @include('partials.timestamps')
        </div>
    </div>
</footer>
