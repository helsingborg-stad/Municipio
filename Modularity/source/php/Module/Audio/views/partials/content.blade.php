<div class="u-display--flex u-justify-content--{{$alignment}}">
    <audio controls src="{{$url}}" @if (!$hideTitle && !empty($postTitle)) aria-labelledby="{{ 'mod-audio-' . $ID . '-label' }}" @endif></audio>
</div>
