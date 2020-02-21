<div class="box box-panel box-panel-secondary">

    @typography([
        'element'=> 'h4',
        'classList' => ['box-title']
    ])
        {{get_the_archive_title()}}
    @endtypography

    <ul>
        @while(have_posts())
            {!! the_post() !!}
            <li>

                @link([
                    'href' =>  get_the_permalink(),
                    'classList' => ['grid-xs-6']
                ])
                    get_the_title()
                @endlink

                @foreach (municipio_post_taxonomies_to_display(get_the_id()) as $taxonomy => $terms)
                    @foreach ($terms as $term)

                        @link([
                            'href' =>  get_term_link($term, $taxonomy),
                            'classList' => [
                                'tag',
                                'tag-'.$term->taxonomy,
                                'tag-'.$term->slug]
                        ])
                            $term->name
                        @endlink

                    @endforeach
                @endforeach

                @if (get_field('archive_' . sanitize_title(get_post_type()) . '_feed_date_published', 'option') != 'false')

                    {{-- TODO: $post->dateObject  something from them post object - use right var --}}
                    @date([
                        'action' => 'formatDate',
                        'timestamp' =>  $post->dateObject
                    ])
                    @enddate

                @endif
            </li>
        @endwhile
    </ul>
</div>
