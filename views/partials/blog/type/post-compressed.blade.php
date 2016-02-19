<?php global $post; ?>
<div class="post post-compressed">
    <a href="{{ the_permalink() }}" class="box box-news box-news-horizontal">
        @if (municipio_get_thumbnail_source())
        <div class="box-image-container">
            <img src="{{ municipio_get_thumbnail_source() }}">
        </div>
        @endif

        <div class="box-content">
            <h3 class="text-highlight">{{ the_title() }}</h3>
            {{ the_excerpt() }}
        </div>
    </a>

    @include('partials.blog.post-footer')
</div>
