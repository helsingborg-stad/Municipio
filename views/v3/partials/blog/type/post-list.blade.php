<div class="box box-panel box-panel-secondary">
    <h4 class="box-title">{{get_the_archive_title()}}</h4>
    <ul>
        @while(have_posts())
            {!! the_post() !!}
            <li>
                <span class="grid">
                    <span class="grid-xs-6">
                        <a href="{{ get_the_permalink() }}" class="link-item">
                            {{ get_the_title() }}
                        </a>
                    </span>

                    <span class="grid-xs-4">
                        @foreach (municipio_post_taxonomies_to_display(get_the_id()) as $taxonomy => $terms)
                            @foreach ($terms as $term)
                                <a href="{{ get_term_link($term, $taxonomy) }}" class="tag tag-{{ $term->taxonomy }} tag-{{ $term->slug }}" style="{{ apply_filters('Municipio/taxonomy/tag_style', 'display: inline;', $term, $taxonomy) }}">{{ $term->name }}</a>
                            @endforeach
                        @endforeach
                    </span>

                    <span class="grid-xs-2">
                        @if (get_field('archive_' . sanitize_title(get_post_type()) . '_feed_date_published', 'option') != 'false')
                        <time class="small">
                            {{ in_array(get_field('archive_' . sanitize_title(get_post_type()) . '_feed_date_published', 'option'), array('datetime', 'date')) ? the_time(get_option('date_format')) : '' }}
                            {{ in_array(get_field('archive_' . sanitize_title(get_post_type()) . '_feed_date_published', 'option'), array('datetime', 'time')) ? the_time(get_option('time_format')) : '' }}
                        </time>
                        @endif
                    </span>
                </span>
            </li>
        @endwhile
    </ul>
</div>
