@include('partials.schema.sponsor.facts', [
    'heading' => __('Practical information', 'municipio'),
    'facts' => [
        ['label' => __('Start date', 'municipio'), 'value' => $sponsor['startDate'], 'icon' => 'event'],
        ['label' => __('End date', 'municipio'), 'value' => $sponsor['endDate'], 'icon' => 'event'],
        ['label' => __('Place', 'municipio'), 'value' => implode('; ', $sponsor['locations']), 'icon' => 'location_on'],
        ['label' => __('Categories', 'municipio'), 'value' => implode(', ', $sponsor['categories']), 'icon' => 'category'],
        ['label' => __('Activities', 'municipio'), 'value' => implode(', ', $sponsor['activities']), 'icon' => 'local_activity'],
        ['label' => __('Deadline', 'municipio'), 'value' => $sponsor['deadline'], 'icon' => 'schedule'],
    ],
])

@if($sponsor['items'])
    <section class="c-stack u-margin__top--5">
        @typography(['element' => 'h2', 'variant' => 'h3', 'classList' => ['u-margin__bottom--0']]){{ __('What is offered', 'municipio') }}@endtypography
        @foreach($sponsor['items'] as $item)
            <div>
                @if($item['name']) @typography(['element' => 'h3', 'variant' => 'h4', 'classList' => ['u-margin__bottom--0']]){{ $item['name'] }}@endtypography @endif
                {!! $item['description'] !!}
            </div>
        @endforeach
    </section>
@endif

@if($sponsor['requirements'])
    <section class="c-stack u-margin__top--5">
        @typography(['element' => 'h2', 'variant' => 'h3', 'classList' => ['u-margin__bottom--0']]){{ $sponsorRequirementsHeading }}@endtypography
        @include('partials.schema.sponsor.requirements', ['requirements' => $sponsor['requirements']])
    </section>
@endif

@foreach($sponsor['offers'] as $offer)
    <section class="c-stack u-margin__top--5">
        @typography(['element' => 'h2', 'variant' => 'h3', 'classList' => ['u-margin__bottom--0']]){{ $offer['name'] ?: $sponsorRequirementsHeading }}@endtypography
        @if($offer['description']) <div>{!! $offer['description'] !!}</div> @endif
        @include('partials.schema.sponsor.requirements', ['requirements' => $offer['requirements']])
        @include('partials.schema.sponsor.facts', [
            'heading' => '',
            'facts' => [
                ['label' => __('Categories', 'municipio'), 'value' => implode(', ', $offer['categories']), 'icon' => 'category'],
                ['label' => __('Deadline', 'municipio'), 'value' => $offer['deadline'], 'icon' => 'schedule'],
            ],
        ])
    </section>
@endforeach

@if($sponsor['contacts'] || array_filter(array_column($sponsor['organizations'], 'contacts')))
    <section class="c-stack u-margin__top--5">
        @typography(['element' => 'h2', 'variant' => 'h3', 'classList' => ['u-margin__bottom--0']]){{ __('Contact', 'municipio') }}@endtypography
        @include('partials.schema.sponsor.contacts', ['contacts' => $sponsor['contacts']])
        @foreach($sponsor['organizations'] as $organization)
            @if($organization['contacts'])
                @if(count($sponsor['organizations']) > 1 && $organization['name'])
                    @typography(['element' => 'h3', 'variant' => 'h4', 'classList' => ['u-margin__bottom--0']]){{ $organization['name'] }}@endtypography
                @endif
                @include('partials.schema.sponsor.contacts', ['contacts' => $organization['contacts']])
            @endif
        @endforeach
    </section>
@endif

@if($sponsor['organizations'])
    <section class="c-stack u-margin__top--5">
        @typography(['element' => 'h2', 'variant' => 'h3', 'classList' => ['u-margin__bottom--0']]){{ $sponsorOrganizationHeading }}@endtypography
        @foreach($sponsor['organizations'] as $organization)
            <div class="c-stack">
                @if($organization['description']) <div>{!! $organization['description'] !!}</div> @endif
                @collection(['unbox' => true, 'bordered' => true])
                    @if($organization['name'])
                        @collection__item(['icon' => 'business', 'classList' => ['u-align-items--center']])
                            @typography()<strong>{{ $sponsorOrganizationLabel }}:</strong> {{ $organization['name'] }}@endtypography
                        @endcollection__item
                    @endif
                    @if($organization['taxID'])
                        @collection__item(['icon' => 'badge', 'classList' => ['u-align-items--center']])
                            @typography()<strong>{{ __('Organization number', 'municipio') }}:</strong> {{ $organization['taxID'] }}@endtypography
                        @endcollection__item
                    @endif
                    @if($organization['url'])
                        @collection__item(['icon' => 'link', 'link' => $organization['url'], 'classList' => ['u-align-items--center']])
                            @typography(){{ __('Visit website', 'municipio') }}@endtypography
                        @endcollection__item
                    @endif
                @endcollection
                @if($organization['requirements'])
                    @typography(['element' => 'h3', 'variant' => 'h4', 'classList' => ['u-margin__bottom--0']]){{ $sponsorRequirementsHeading }}@endtypography
                    @include('partials.schema.sponsor.requirements', ['requirements' => $organization['requirements']])
                @endif
            </div>
        @endforeach
    </section>
@endif
