<div class="o-grid o-grid--half-gutter{{ !empty($stretch) ? ' o-grid--stretch' : '' }}" {{!empty($freeTextFiltering) ? 'js-filter-container=' . $ID : ''}}>
    @if($accordionSpacedSections)
        @include('partials.accordion')
    @else
        @card([
            'context' => $context,
        ])
            @include('partials.accordion', ['divider' => true])
        @endcard
    @endif
</div>