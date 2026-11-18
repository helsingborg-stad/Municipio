<div id="article" class="c-sponsor u-width--100" tabindex="-1">
    @typography(['element' => 'p', 'classList' => ['c-typography__variant--lead', 'u-margin__top--0', 'u-margin__bottom--2']])
        {{ $sponsorTypeLabel }}@if($sponsor['organization']['name'])<span class="u-margin__x--1">/</span>{{ $sponsor['organization']['name'] }}@endif
    @endtypography
    @typography(['element' => 'h1', 'variant' => 'h1', 'id' => 'page-title'])
        {{ $post->getTitle() }}
    @endtypography

    @if($image = $post->getImage())
        @include('partials.schema.sponsor.image', ['imageSrc' => $image, 'imageAlt' => ''])
    @endif

    @if($sponsor['description'])
        <section class="c-stack u-margin__top--4">
            @typography(['element' => 'h2', 'variant' => 'h3', 'classList' => ['u-margin__bottom--0']]){{ $sponsorDescriptionHeading }}@endtypography
            <div>{!! $sponsor['description'] !!}</div>
        </section>
    @endif

    @include('partials.schema.sponsor.details')
</div>
