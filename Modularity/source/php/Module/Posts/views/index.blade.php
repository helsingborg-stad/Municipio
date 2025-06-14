@if($archiveLinkAbovePosts)
    <div class="o-grid u-align-items--center">
        <div class="o-grid-9@md">
            @include('partials.head')
        </div>
        <div class="o-grid-3@md">
            @include('partials.more-link')
        </div>
    </div>
@else
    @include('partials.head')
@endif

<div class="o-grid{{ !empty($stretch) ? ' o-grid--stretch' : '' }}{{ !empty($noGutter) ? ' o-grid--no-gutter' : '' }}{{ (!empty($preamble)||(!$hideTitle && !empty($postTitle))) ? ' u-margin__top--4' : '' }}"
@if (!$hideTitle && !empty($postTitle)) aria-labelledby="{{ 'mod-posts-' . $ID . '-label' }}" @endif>
    @if($posts)    
        @foreach ($posts as $post)
            <div class="{{!empty($post->classList) ? implode(' ', $post->classList) : ''}}" 
            {{!empty($post->attributeList) ? implode(' ', array_map(function($key, $value) {
                return $key . '=' . $value;
            }, array_keys($post->attributeList), $post->attributeList)) : '' }}>
                @if ($highlight_first_column_as === 'block' && $post->isHighlighted)
                    @include('partials.post.block', ['ratio' => '16:9'])
                @else
                    @include('partials.post.card')
                @endif
            </div>
        @endforeach
    @endif
</div>

@includeWhen(!$archiveLinkAbovePosts, 'partials.more')
