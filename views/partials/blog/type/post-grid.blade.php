<?php global $post; $thumbnail = municipio_get_thumbnail_source($post->ID, array(400, 300)); ?>
<div class="{{ !empty(get_field('blog_grid_columns', 'option')) ? get_field('blog_grid_columns', 'option') : 'grid-md-6' }}">
    <a href="{{ the_permalink() }}" class="box box-post-brick">
        @if ($thumbnail)
        <div class="box-image" {!! $thumbnail ? 'style="background-image:url(' . $thumbnail . ');"' : '' !!}>
            <img src="{{ municipio_get_thumbnail_source() }}" alt="{{ the_title() }}">
        </div>
        @endif

        <div class="box-content">
            <span class="box-post-brick-date">
                <time>{{ the_time(get_option('date_format')) }} {{ the_time(get_option('time_format')) }}</time>
            </span>
            <h3 class="post-title">{{ the_title() }}</h3>
        </div>
        <div class="box-post-brick-lead">
            {{ the_excerpt() }}
        </div>
    </a>
</div>
