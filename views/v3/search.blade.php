@extends('templates.master')
@section('layout')


    {{-- TODO: Unuglify this. Loads of functionality must be moved to controller. 
        
        Issues: 
        - Functions in view should be used in controller.
        - Translations string should be placed in string translation object.     
        - Untranslated string should be translated. 
        - Datafetching in post should be moved. Would be nice to use the item templates. 
        - Get vars should be utilized in controller. Get query var func should be used. 
        
    --}}

    <?php global $wp_query; ?>

    <section>

        @form([
            'method' => 'get',
            'action' => esc_url( home_url( '/' ) ),
            'classList' => ['u-margin__bottom--4']
        ])
            @field([
                'type' => 'text',
                'value' => get_search_query(),
                'attributeList' => [
                    'type' => 'search',
                    'name' => 's',
                    'required' => false,
                ],
                'label' => _x( 'Search for:', 'label' )
            ])
            @endfield
        @endform

        <div class="search-result-count">
            @typography(['variant' => 'h2', 'element' => 'h2'])
            Found {{ $resultCount }} hits 
            @endtypography
        </div>

    </section>

    <?php do_action('search_notices'); ?>

    @if ($resultCount === 0)
        <?php _e('Found no matching results on your search…', 'municipio'); ?>
    @else

    <section class="u-mt-0 u-margin__top--2">

        @if ($template === 'grid')
                    
            @while(have_posts())
            
                {!! the_post() !!}
                    <?php
                    $date = apply_filters('Municipio/search_result/date', get_the_modified_date(), get_post());
                    $permalink = apply_filters('Municipio/search_result/permalink_url', get_permalink(), get_post());
                    $permalinkText = apply_filters('Municipio/search_result/permalink_text', get_permalink(), get_post());
                    $title = apply_filters('Municipio/search_result/title', get_the_title(), get_post());
                    $lead = apply_filters('Municipio/search_result/excerpt', get_the_excerpt(), get_post());
                    $thumbnail = wp_get_attachment_image_src(
                        get_post_thumbnail_id(),
                        apply_filters('Modularity/index/image', municipio_to_aspect_ratio('16:9', array(500, 500))                                            )
                    );
                    if (is_array($thumbnail)) {
                        $thumbnail = $thumbnail[0];
                    }
                    ?>
                    @includeIf('partials.search.result-item-grid')
            @endwhile

        @else
            
                    @foreach($searchResult as $result)
                            @includeIf('partials.search.result-item', ['searchResult' => $result])
                    @endforeach
            
            </ul>
        @endif


        @if ($wp_query->max_num_pages > 1)

            @pagination(['list' => $pagination,
            'current' => isset($_GET['pagination']) ? $_GET['pagination'] : 1])
            @endpagination

        @endif

    </section>

    @endif

@stop