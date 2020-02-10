<?php global $post; ?>
<div class="post post-horizontal">
    <div class="grid">
        <div class="{{ municipio_get_thumbnail_source(null, array(400, 350)) ? 'grid-md-7 grid-sm-12' : 'grid-md-12' }} post-horizontal__content">
            <header class="post-header">
                <h2><a href="{{ the_permalink() }}">{{ the_title() }}</a></h2>
                @include('partials.blog.post-info')
            </header>
            <article>
                {{ the_excerpt() }}
            </article>
            @if (!empty(municipio_post_taxonomies_to_display(get_the_id())))
                @foreach (municipio_post_taxonomies_to_display(get_the_id()) as $taxonomy => $terms)
                    <strong class="text-sm">{{ count($terms) == 1 ? get_taxonomy($taxonomy)->labels->singular_name : get_taxonomy($taxonomy)->label }}:</strong>
                    <ul class="inline-block nav-horizontal tags">
                        @foreach ($terms as $term)
                            <li><a href="{{ get_term_link($term, $taxonomy) }}" class="label label-sm" style="{{ apply_filters('Municipio/taxonomy/tag_style', '', $term, $taxonomy) }}">{{ $term->name }}</a></li>
                        @endforeach
                    </ul>
                @endforeach
            @endif
        </div>
        @if (municipio_get_thumbnail_source(null, array(650, 500)))
            <div class="grid-md-5 grid-sm-12 post-horizontal__image-container">
                <div class="post-horizontal__image" style="background:url('{!! municipio_get_thumbnail_source(null, array(650, 500)) !!}') no-repeat center center;"></div>
            </div>
        @endif
    </div>
    <footer class="post-footer">
        @if (get_field('post_show_share', get_the_id()) !== false)
            <div class="text-right">
                @include('partials.social-share')
            </div>
        @endif
    </footer>
</div>
