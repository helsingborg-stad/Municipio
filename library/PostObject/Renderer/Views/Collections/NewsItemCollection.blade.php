<div class="arcive-news-items o-grid">
    @foreach ($postObjects as $postObject)
        @include('Items.NewsItem', ['postObject' => $postObject])
    @endforeach
</div>