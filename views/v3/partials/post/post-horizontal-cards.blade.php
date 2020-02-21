{{-- TODO: reformat as card component --}}
<?php global $post; ?>
<div class="post post-horizontal">
    <div class="grid">
        <div class="{{ municipio_get_thumbnail_source(null, array(400, 350)) ? 'grid-md-7 grid-sm-12' : 'grid-md-12' }} post-horizontal__content">
            <header class="post-header">
                @link([
                    'href' => the_permalink()
                ])
                    @typography([
                        "variant" => "h2",
                        "element" => "h2",
                    ])
                        {{the_title()}}
                    @endtypography
                @endlink

                @includeIf('partials.blog.post-info')

            </header>
            <article>
                {{ the_excerpt() }}
            </article>
            @if (!empty(municipio_post_taxonomies_to_display(get_the_id())))
                <ul class="inline-block nav-horizontal tags">
                    @foreach (municipio_post_taxonomies_to_display(get_the_id()) as $taxonomy => $terms)
                        @foreach ($terms as $term)
                            <li><span class="label label-sm">{{ $term->name }}</span></li>
                        @endforeach
                    @endforeach
                </ul>
            @endif
        </div>
        @if (municipio_get_thumbnail_source(null, array(650, 500)))
            <div class="post-horizontal__image" style="background:url('{!! municipio_get_thumbnail_source(null, array(650, 500)) !!}') no-repeat center center;"></div>
        @endif
    </div>
    <footer class="post-footer">
        {{-- Contains something --}}
    </footer>
</div>
