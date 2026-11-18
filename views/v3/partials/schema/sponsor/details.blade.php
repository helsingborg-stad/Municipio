@include('partials.schema.sponsor.facts', [
    'heading' => false,
    'facts' => [
        ['label' => __('Support date', 'municipio'), 'value' => $sponsor['supportDate'], 'icon' => 'event'],
        ['label' => __('Place', 'municipio'), 'value' => $sponsor['location'], 'icon' => 'location_on'],
        ['label' => __('Resources', 'municipio'), 'value' => $sponsor['resources'], 'icon' => 'category'],
        ['label' => __('Activities', 'municipio'), 'value' => $sponsor['activities'], 'icon' => 'local_activity'],
        ['label' => __('Sponsor preferences', 'municipio'), 'value' => $sponsor['preferences'], 'icon' => 'info'],
        ['label' => __('Practical information', 'municipio'), 'value' => $sponsor['practicalInformation'], 'icon' => 'info'],
        ['label' => __('Counter-performance', 'municipio'), 'value' => $sponsor['counterPerformance'], 'icon' => 'info'],
    ],
])

@if($sponsor['contacts'])
    <section class="c-stack u-margin__top--5">
        @typography(['element' => 'h2', 'variant' => 'h3']){{ __('Contact', 'municipio') }}@endtypography
        @include('partials.schema.sponsor.contacts', ['contacts' => $sponsor['contacts']])
    </section>
@endif

@if(array_filter($sponsor['organization']))
    <section class="c-stack u-margin__top--5">
        @typography(['element' => 'h2', 'variant' => 'h3']){{ $sponsorOrganizationHeading }}@endtypography
        @if($sponsor['organization']['name'])
            @typography(['element' => 'p']){{ $sponsor['organization']['name'] }}@endtypography
        @endif
        @if($sponsor['organization']['description'])
            <div>{!! $sponsor['organization']['description'] !!}</div>
        @endif
        @if($sponsor['organization']['url'])
            <a href="{{ $sponsor['organization']['url'] }}">{{ __('Website', 'municipio') }}</a>
        @endif
    </section>
@endif
