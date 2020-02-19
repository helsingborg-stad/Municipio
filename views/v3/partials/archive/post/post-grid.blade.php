@php
global $post;

$thumbnail = municipio_get_thumbnail_source(
    $post->ID,
    array(500, 500)
);
;
@endphp
@link([
    'href' =>  the_permalink(),
    'classList' => [
        'box',
        'box-post-brick',
        $grid_alter ? 'brick-columns-' . $gridSize : ''
    ]
])


        @if ($thumbnail)
        <div class="box-image" {!! $thumbnail ? 'style="background-image:url(' . $thumbnail . ');"' : '' !!}>

            @image([
                'src'=> municipio_get_thumbnail_source(null,array(500,500)),
                'alt' => the_title()
            ])
            @endimage

        </div>
        @endif

        <div class="box-content">

            @if (in_array('category', (array)get_field('archive_' . sanitize_title(get_post_type()) . '_post_display_info', 'option')) && isset(get_the_category()[0]->name))

                @typography([
                    'variant'=> 'span',
                    'classList' => ['box-post-brick-category']
                ])
                    get_the_category()[0]->name
                @endtypography

            @endif

            @if (get_field('archive_' . sanitize_title(get_post_type()) . '_feed_date_published', 'option') != 'false')
            <span class="box-post-brick-date u-mb-3">

                @php
                    $date = in_array(get_field('archive_' . sanitize_title(get_post_type()) .
                        '_feed_date_published', 'option'), array('datetime', 'date')) ?
                        the_time(get_option('date_format')) : '' ;

                    $time =  in_array(get_field('archive_' . sanitize_title(get_post_type()) .
                        '_feed_date_published', 'option'), array('datetime', 'time')) ?
                        the_time(get_option('time_format')) : '';

                @endphp

                @date([
                    'action' => 'formatDate',
                    'timestamp' =>  $date . ' ' . $time
                ])
                @enddate

            </span>
            @endif

            <h3 class="post-title">{{ the_title() }}</h3>

            <ul class="tags">
                @foreach (municipio_post_taxonomies_to_display(get_the_id()) as $taxonomy => $terms)
                    @foreach ($terms as $term)
                        <li class="tag tag-{{ $term->taxonomy }} tag-{{ $term->slug }}">{{ $term->name }}</li>
                    @endforeach
                @endforeach
            </ul>

        </div>
        <div class="box-post-brick-lead">
            {{ the_excerpt() }}
        </div>
@endlink

