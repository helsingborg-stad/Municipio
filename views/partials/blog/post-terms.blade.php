<h5>{{ get_taxonomy($taxonomy)->label }}</h5>
<ul class="tags">
    @foreach ($terms as $term)
    <li><a href="{{ get_term_link($term, $taxonomy) }}" class="tag tag-{{ $term->taxonomy }} tag-{{ $term->slug }}">{{ $term->name }}</a></li>
    @endforeach
</ul>
