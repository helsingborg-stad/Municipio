<?php
global $post;

$thumbnail = municipio_get_thumbnail_source(
    $post->ID,
    array(500, 500)
);

$columnSize = \Municipio\Controller\Archive::getColumnSize();
?>
<div class="{{ $columnSize }}">
    <a href="{{ the_permalink() }}" class="box box-post-brick <?php echo $grid_alter ? 'brick-columns-' . $gridSize . '"' : ''; ?>">
        @if ($thumbnail)
        <div class="box-image" {!! $thumbnail ? 'style="background-image:url(' . $thumbnail . ');"' : '' !!}>
            <img src="{{ municipio_get_thumbnail_source(null,array(500,500)) }}" alt="{{ the_title() }}">
        </div>
        @endif

        <div class="box-content">

            @if (in_array('category', (array)get_field('archive_' . sanitize_title(get_post_type()) . '_post_display_info', 'option')) && isset(get_the_category()[0]->name))
            <span class="box-post-brick-category">{{ get_the_category()[0]->name }}</span>
            @endif

            @if (get_field('archive_' . sanitize_title(get_post_type()) . '_feed_date_published', 'option') != 'false')
            <span class="box-post-brick-date">
                <time>
                    {{ in_array(get_field('archive_' . sanitize_title(get_post_type()) . '_feed_date_published', 'option'), array('datetime', 'date')) ? the_time(get_option('date_format')) : '' }}
                    {{ in_array(get_field('archive_' . sanitize_title(get_post_type()) . '_feed_date_published', 'option'), array('datetime', 'time')) ? the_time(get_option('time_format')) : '' }}
                </time>
            </span>
            <br>
            <br>
            @endif

            @foreach (get_post_taxonomies() as $key=>$value)
                @php $terms =  wp_get_post_terms($post->ID, $value); @endphp
                <ul class="tags tags-{{$value}}">
                    @foreach ($terms as $term)
                        <li class="tag tag-{{ $term->taxonomy }} tag-{{ $term->slug }}">{{ $term->name }}</li>
                    @endforeach
                </ul>
            @endforeach
            <h3 class="post-title">{{ the_title() }}</h3>

        </div>
        <div class="box-post-brick-lead">
            {{ the_excerpt() }}
        </div>
    </a>
</div>
