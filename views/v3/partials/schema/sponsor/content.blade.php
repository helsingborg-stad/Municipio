@php
    $isDemand = $post->getSchema() instanceof \Municipio\Schema\SponsorDemand;
    $typeLabel = $isDemand ? __('Sponsorship request', 'municipio') : __('Sponsorship offer', 'municipio');

    $image = $post->getImage();
    $facts = [
        ['label' => $isDemand ? __('Support needed by', 'municipio') : __('Sponsorship date', 'municipio'), 'value' => $sponsor['supportDate'], 'icon' => 'event'],
        ['label' => __('Sponsorship location', 'municipio'), 'value' => $sponsor['location'], 'icon' => 'location_on'],
        ['label' => $isDemand ? __('What the association needs', 'municipio') : __('What the company wants to contribute', 'municipio'), 'value' => $sponsor['resources'], 'icon' => 'category'],
        ['label' => __('Main activity of the association', 'municipio'), 'value' => $sponsor['activities'], 'icon' => 'local_activity'],
    ];

    $conditions = [
        ['label' => __('What the association looks for in a sponsor', 'municipio'), 'value' => $sponsor['preferences']],
        ['label' => __('Things to keep in mind', 'municipio'), 'value' => $sponsor['practicalInformation']],
        ['label' => $isDemand ? __('What the association can offer in return', 'municipio') : __('What the company would like in return', 'municipio'), 'value' => $sponsor['counterPerformance']],
    ];

@endphp

<div id="article" class="u-width--100 o-grid o-grid--no-gutter u-justify-content--center" tabindex="-1">
    <header class="o-grid-12 o-grid-8@md">
        <div>
            @typography(['element' => 'p', 'variant' => 'lead', 'classList' => ['u-margin__bottom--2']])
                {{ $typeLabel }}@if($sponsor['organization']['name']) · {{ $sponsor['organization']['name'] }}@endif
            @endtypography
            @typography(['element' => 'h1', 'variant' => 'h1', 'id' => 'page-title']){{ $post->getTitle() }}@endtypography
            @if($sponsor['contacts'])
                <a class="u-display--inline-block u-margin__top--2" href="#sponsor-contact"><strong>{{ __('Go to contact details', 'municipio') }}</strong></a>
            @endif
        </div>
        @if($image)
            <div>
                @include('partials.schema.sponsor.image', ['imageSrc' => $image, 'imageAlt' => ''])
            </div>
        @endif
    </header>

    <div class="o-grid-12 o-grid-6@md">
        @include('partials.schema.sponsor.details')
    </div>
</div>
