@if (empty($hideTitle) && !empty($postTitle))
    @typography([
        'id'        => 'mod-text-' . $ID .'-label',
        'element'   => 'h2', 
        'variant'   => 'h2', 
        'classList' => ['module-title']
    ])
        {!! $postTitle !!}
    @endtypography
@endif

<div class="o-grid o-grid--half-gutter" @if (!$hideTitle && !empty($postTitle)) aria-labelledby="{{ 'mod-text-' . $ID . '-label' }}" @endif>
    @foreach ($contacts as $contact)
        <div class="o-grid-12 {{apply_filters('Municipio/Controller/Archive/GridColumnClass', $columns)}}">
            @card([
                'attributeList' => [
                    'itemscope'     => '',
                    'itemtype'      => 'http://schema.org/Person'
                ],
                'classList'     => [
                    'u-height--100',
                    'c-card--contact'
                ],
                'context' => 'module.contacts.card'
            ])
                <div class="c-card__body u-padding--0">
                    @signature([
                        'author' => $contact['full_name'] ?? '', 
                        'authorRole' => $contact['full_title'],
                        'avatar' => $contact['thumbnail'][0] ?? null,
                        'placeholderAvatar' => isset($placeholder_avatar) ? $placeholder_avatar : true,
                        'classList' => ['u-margin--2'],
                    ])
                    @endsignature

                    {{-- Other content data --}}
                    @includeWhen(!empty($contact['other']), 'components.other')

                    @accordion([])
                        {{-- Opening Hours --}}
                        @includeWhen(!empty($contact['opening_hours']), 'components.openinghours')

                        {{-- Adress --}}
                        @includeWhen(!empty($contact['address']), 'components.adress')

                        {{-- Visiting Address --}}
                        @includeWhen(!empty($contact['visiting_address']), 'components.visiting')
                        
                    @endaccordion
                </div>

                @if(array_filter([!empty($contact['email']), !empty($contact['phone'])]))

                    <div class="u-border__top--1 u-margin__top--auto u-padding__x--2" style="gap: var(--base, 8px); border-top-color: var(--color-border-divider) !important;">

                        {{-- E-mail --}}
                        @includeWhen(!empty($contact['email']), 'components.email', ['icon' => 'email'])

                        {{-- Phone --}}
                        @if (!empty($contact['phone']))
                            @foreach ($contact['phone'] as $phone)
                                @include('components.phone', $phone)
                            @endforeach
                        @endif

                        {{-- Some --}}
                        @if (!empty($contact['social_media']))
                            @foreach ($contact['social_media'] as $some)
                                @include('components.some', $some)
                            @endforeach
                        @endif

                    </div>

                @endif


            @endcard
        </div>
    @endforeach
</div>
