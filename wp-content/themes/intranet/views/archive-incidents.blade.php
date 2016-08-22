@extends('templates.master')

@section('content')

<div class="container main-container">
    @include('partials.breadcrumbs')

    <div class="grid {{ (wp_get_post_parent_id(get_the_id()) != 0) ? 'no-margin-top' : '' }}">
        @include('partials.sidebar-left')

        <div class="{{ $contentGridSize }} print-grow">
            <div class="grid">
                <div class="grid-xs-12">
                    <ul class="search-result-list">
                        @while(have_posts())
                            {!! the_post() !!}
                            <?php global $post; ?>
                            <li>
                                <div class="search-result-item">
                                    <h3 class="gutter gutter-bottom gutter-sm"><a href="{{ get_blog_permalink($post->blog_id, $post->ID) }}" class="pricon pricon-notice-{{ $post->incident_level }} pricon-space-right notice notice-inline-block notice-sm {{ $post->incident_level }}">{{ the_title() }}</a></h3>
                                    <span class="network-title-format label label-sm label-creamy"><?php echo municipio_intranet_format_site_name(\Intranet\Helper\Multisite::getSite($post->blog_id)); ?></span>
                                    <p><?php echo isset(get_extended($post->post_content)['extended']) ? wp_strip_all_tags(get_extended($post->post_content)['main']) : wp_trim_words($post->post_content, 50, ''); ?></p>
                                    <div class="search-result-info small">
                                        @if (get_field('start_date') || get_field('end_date'))
                                        <strong><?php _e('Duration', 'municipio-intranet'); ?>:</strong> {{ get_field('start_date') ? date('Y-m-d H:i', strtotime(get_field('start_date'))) : '' }} <?php echo get_field('end_date') ? __('to', 'municipio-intranet') . ' ' . date('Y-m-d H:i', strtotime(get_field('end_date'))) : ''; ?><br>
                                        @endif
                                        <strong><?php _e('Published', 'municipio-intranet'); ?>:</strong> {{ the_time('Y-m-d H:i') }}
                                    </div>
                                </div>
                            </li>
                        @endwhile
                    </ul>
                </div>
            </div>

            @if (municipio_show_posts_pag())
            <div class="grid">
                <div class="grid-sm-12 text-center">
                    {!!
                        paginate_links(array(
                            'type' => 'list'
                        ))
                    !!}
                </div>
            </div>
            @endif
        </div>

        @include('partials.sidebar-right')
    </div>
</div>

@stop
