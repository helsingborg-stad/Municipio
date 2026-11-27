@if($sponsor['description'])
    <section class="u-margin__top--5">
        @typography(['element' => 'h2', 'variant' => 'h3'])
            @include('partials.schema.sponsor.label', ['labelKey' => 'description'])
        @endtypography
        {!! $sponsor['description'] !!}
    </section>
@endif

@foreach($conditions as $condition)
    @if($condition['value'] !== '')
        <section class="u-margin__top--5">
            @typography(['element' => 'h2', 'variant' => 'h3'])@include('partials.schema.sponsor.label', ['labelKey' => $condition['label']])@endtypography
            @typography(['element' => 'p']){{ $condition['value'] }}@endtypography
        </section>
    @endif
@endforeach

@if(array_filter(array_column($facts, 'value')))
    <div class="u-margin__top--5">
        @typography(['element' => 'h2', 'variant' => 'h3', 'classList' => ['u-margin__top--2']])
            @include('partials.schema.sponsor.label', ['labelKey' => 'summary'])
        @endtypography
        @collection(['unbox' => true, 'bordered' => true, 'classList' => ['u-margin__top--2']])
            @foreach($facts as $fact)
                @if($fact['value'] !== '')
                    @collection__item(['icon' => $fact['icon'], 'classList' => ['u-align-items--center']])
                        @typography(['element' => 'p', 'variant' => 'meta'])@include('partials.schema.sponsor.label', ['labelKey' => $fact['label']])@endtypography
                        @typography(['element' => 'p', 'classList' => ['u-margin__top--0']])
                            <strong>{{ $fact['value'] }}</strong>
                        @endtypography
                    @endcollection__item
                @endif
            @endforeach
        @endcollection
    </div>
@endif

@if($sponsor['contacts'])
    <section class="u-margin__top--5" id="sponsor-contact" tabindex="-1">
        @typography(['element' => 'h2', 'variant' => 'h3', 'classList' => ['u-margin__bottom--2']])
            @include('partials.schema.sponsor.label', ['labelKey' => 'contacts'])
        @endtypography
        @card([])
            @include('partials.schema.sponsor.contacts', ['contacts' => $sponsor['contacts']])
        @endcard
    </section>
@endif

@if(array_filter($sponsor['organization']))
    <section class="u-margin__top--5">
        @typography(['element' => 'h2', 'variant' => 'h3'])
            @include('partials.schema.sponsor.label', ['labelKey' => 'organization'])
        @endtypography
        @if($sponsor['organization']['name'])
            @typography(['element' => 'p'])<strong>{{ $sponsor['organization']['name'] }}</strong>@endtypography
        @endif
        {!! $sponsor['organization']['description'] !!}
        @if($sponsor['organization']['url'])
            <p><a href="{{ $sponsor['organization']['url'] }}" target="_blank" rel="noopener noreferrer">{{ $sponsorTemplate['labels']['organization_link'] }}</a>
                @include('partials.schema.sponsor.edit-label', ['labelKey' => 'organization_link'])
            </p>
        @endif
    </section>
@endif
