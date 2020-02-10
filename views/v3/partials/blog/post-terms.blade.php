<strong>{{ get_taxonomy($taxonomy)->label }}:</strong>
<ul class="inline-block nav-horizontal tags">
    @foreach ($terms as $term)
    <li><a href="{{ get_term_link($term, $taxonomy) }}" class="tag tag-{{ $term->taxonomy }} tag-{{ $term->slug }}" style="{{ apply_filters('Municipio/taxonomy/tag_style', '', $term, $taxonomy) }}">{{ $term->name }}</a></li>
    @endforeach
</ul>
