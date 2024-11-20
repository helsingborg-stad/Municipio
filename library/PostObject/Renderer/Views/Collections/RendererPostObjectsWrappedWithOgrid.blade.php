<div class="o-grid">
    @foreach($postObjects as $postObject)
        @include($postObjectView, ['postObject' => $postObject])
    @endforeach
</div>