<div class="o-grid{{ !empty($stretch) ? ' o-grid--stretch' : '' }}{{ !empty($noGutter) ? ' o-grid--no-gutter' : '' }}{{ (!empty($preamble)||(!$hideTitle && !empty($postTitle))) ? ' u-margin__top--4' : '' }}"
@if (!$hideTitle && !empty($postTitle)) aria-labelledby="{{ 'mod-posts-' . $ID . '-label' }}" @endif>
    @foreach ($postObjects as $postObject)
            @if ($loop->first && $highlight_first_column && $highlight_first_column_as === 'block')
                <div class="{{ $highlight_first_column }}">
                    @include('Items.BlockItem', ['ratio' => '16:9'])
                </div>
            @else
                <div class="{!! $gridColumnClass !!}">
                    @include('Items.CardItem')
                </div>
            @endif
        </div>
    @endforeach
</div>