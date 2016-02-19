@if (has_tag())
<div class="post-tags-wrapper">
    Tags:
    <ul class="tags tags-white tags">
        {{ the_tags('<li>', '</li><li>', '</li>') }}
    </ul>
</div>
@endif
