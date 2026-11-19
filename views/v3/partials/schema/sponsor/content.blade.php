@php
    $isDemand = $post->getSchema() instanceof \Municipio\Schema\SponsorDemand;
    $typeLabel = $isDemand ? 'Sponsoruppdrag' : 'Sponsorerbjudande';

    $image = $post->getImage();
    $facts = [
        ['label' => $isDemand ? 'Backningen behövs senast' : 'Datum för backningen', 'value' => $sponsor['supportDate'], 'icon' => 'event'],
        ['label' => 'Plats för backningen', 'value' => $sponsor['location'], 'icon' => 'location_on'],
        ['label' => $isDemand ? 'Det här behöver föreningen' : 'Det här vill företaget bidra med', 'value' => $sponsor['resources'], 'icon' => 'category'],
        ['label' => 'Föreningens huvudsakliga verksamhet', 'value' => $sponsor['activities'], 'icon' => 'local_activity'],
    ];

    $conditions = [
        ['label' => 'Det här söker föreningen hos en backare', 'value' => $sponsor['preferences']],
        ['label' => 'Särskilt att tänka på', 'value' => $sponsor['practicalInformation']],
        ['label' => $isDemand ? 'Det här kan föreningen erbjuda som tack' : 'Det här önskar företaget i gengäld', 'value' => $sponsor['counterPerformance']],
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
                <a class="u-display--inline-block u-margin__top--2" href="#sponsor-contact"><strong>Till kontaktuppgifter</strong></a>
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
