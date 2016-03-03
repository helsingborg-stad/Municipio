@if (has_category())
<div class="post-categories-wrapper">
    {{ _e('Categories', 'municipio') }}:
    {{ the_category() }}
</div>
@endif
