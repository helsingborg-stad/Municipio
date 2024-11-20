<div class="o-grid{{ !empty($stretch) ? ' o-grid--stretch' : '' }}{{ !empty($noGutter) ? ' o-grid--no-gutter' : '' }}{{ (!empty($preamble)||(!empty($heading))) ? ' u-margin__top--4' : '' }}"
    aria-labelledby="{{ 'mod-posts-' . $ID . '-label' }}">
    @foreach($postObjects as $postObject)
        <div class="{{ $gridColumnClass }}">
            @include('Items.BoxItem', ['postObject' => $postObject])
        </div>
    @endforeach
</div>
