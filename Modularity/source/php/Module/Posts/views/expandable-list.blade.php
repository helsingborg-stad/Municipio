<div class="o-grid{{ !empty($stretch) ? ' o-grid--stretch' : '' }}">
    <div class="o-grid-12">
        @card([
            'heading' => false,
            'attributeList' => [
                'js-filter-container' => $ID,
                ...(!$hideTitle && !empty($postTitle) ? ['aria-labelledby' => 'mod-posts-' . $ID . '-label'] : []),
            ],
            'context' => 'module.posts.expandablelist'
        ])
        @if ((!$hideTitle && !empty($postTitle)) || !empty($titleCTA))
            <div class="c-card__header">
                @include('partials.post-title', ['variant' => 'h4', 'classList' => [], 'titleCTA' => $titleCTA ?? null])
            </div>
        @endif
            <div>
                @if (!isset($allow_freetext_filtering) || $allow_freetext_filtering)
                    <div class="c-card__body" aria-label="{{ __('Search', 'municipio') }}">
                        @field([
                            'type' => 'search',
                            'name' => 'search',
                            'label' => __('Search', 'municipio'),
                            'hideLabel' => true,
                            'attributeList' => [
                                'js-filter-input' => $ID
                            ],
                            'placeholder' => __('Search', 'municipio')
                        ])
                        @endfield
                    </div>
                @endif

                @if (isset($posts_list_column_titles) && $posts_list_column_titles)

                    <header class="accordion-table__head">

                        @if (!$posts_hide_title_column)
                            @typography([
                                'element' => 'span',
                                'classList' => ['accordion-table__head-column']
                            ])
                                {{ isset($title_column_label) && !empty($title_column_label) && is_string($title_column_label) ? $title_column_label : __('Title', 'modularity') }}
                            @endtypography
                        @endif

                        @if (is_array($posts_list_column_titles) && !empty($posts_list_column_titles))
                            @foreach ($posts_list_column_titles as $column)
                                @typography([
                                    'element' => 'span',
                                    'classList' => ['accordion-table__head-column']
                                ])
                                    {{ $column['column_header'] }}
                                @endtypography
                            @endforeach
                            <span class="accordion-table__head-column-icon"></span>
                        @endif
                    </header>
                @endif


                @if (count($prepareAccordion) > 0)
                    @accordion([])
                        @foreach ($prepareAccordion as $accordionItem)
                            @if (isset($accordionItem['column_values']) &&
                                    is_array($accordionItem['column_values']) &&
                                    !empty($accordionItem['column_values']))
                                @if ($posts_hide_title_column)
                                    @php $accordionItem['heading'] = []; @endphp
                                @endif
                                @accordion__item([
                                    'heading' => $accordionItem['column_values']
                                        ? array_merge((array) $accordionItem['heading'], (array) $accordionItem['column_values'])
                                        : $accordionItem['heading'],
                                    'attributeList' => array_merge(
                                        ['js-filter-item' => '','js-filter-data' => ''], 
                                        $accordionItem['attributeList']
                                    ),
                                    'classList' => array_merge(
                                        $accordionItem['classList'], 
                                        ['c-accordion-table', 'u-clearfix']
                                    )
                                ])
                                    {!! $accordionItem['content'] !!}
                                @endaccordion__item
                            @else
                                @accordion__item([
                                    'heading' => $accordionItem['heading'],
                                    'classList' => $accordionItem['classList'],
                                    'attributeList' => array_merge(
                                        ['js-filter-item' => '','js-filter-data' => ''], 
                                        $accordionItem['attributeList'])
                                ])
                                    {!! $accordionItem['content'] !!}
                                @endaccordion__item
                            @endif
                        @endforeach
                    @endaccordion
                @else
                    <section class="accordion-section">
                        @typography([
                            'element' => 'p'
                        ])
                            {{ __('Nothing to display…', 'modularity') }}
                        @endtypography
                    </section>
                @endif

            </div>

        @endcard
    </div>
</div>

@include('partials.more')