@php
    $image = $post->getImage();
    $facts = [
        ['label' => 'support_date', 'value' => $sponsor['supportDate'], 'icon' => 'event'],
        ['label' => 'location', 'value' => $sponsor['location'], 'icon' => 'location_on'],
        ['label' => 'resources', 'value' => $sponsor['resources'], 'icon' => 'category'],
        ['label' => 'activities', 'value' => $sponsor['activities'], 'icon' => 'local_activity'],
    ];

    $conditions = [
        ['label' => 'preferences', 'value' => $sponsor['preferences']],
        ['label' => 'practical_information', 'value' => $sponsor['practicalInformation']],
        ['label' => 'counter_performance', 'value' => $sponsor['counterPerformance']],
    ];

@endphp

<div id="article" class="u-width--100 o-grid o-grid--no-gutter u-justify-content--center" tabindex="-1">
    <header class="o-grid-12 o-grid-8@md">
        <div>
            @typography(['element' => 'p', 'variant' => 'lead', 'classList' => ['u-margin__bottom--2']])
                @include('partials.schema.sponsor.label', ['labelKey' => 'type'])@if($sponsor['organization']['name']) · {{ $sponsor['organization']['name'] }}@endif
            @endtypography
            @typography(['element' => 'h1', 'variant' => 'h1', 'id' => 'page-title']){{ $post->getTitle() }}@endtypography
            @if($sponsor['contacts'])
                <a class="u-display--inline-block u-margin__top--2" href="#sponsor-contact"><strong>{{ $sponsorTemplate['labels']['contact_link'] }}</strong></a>
                @include('partials.schema.sponsor.edit-label', ['labelKey' => 'contact_link'])
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
