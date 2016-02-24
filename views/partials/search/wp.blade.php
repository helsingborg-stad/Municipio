<section class="creamy gutter-vertical gutter-lg clearfix">
    <div class="container">
        <div class="gid">
            <div class="grid-lg-12">
                {!! get_search_form() !!}
                <div class="gutter gutter-sm gutter-top">
                    <strong>{{ $resultCount }}</strong> träffar på <strong>"{{ $keyword }}"</strong> inom {{ ucfirst(explode('//', home_url(), 2)[1]) }}
                </div>
            </div>
        </div>
    </div>
</section>

@if ($resultCount === 0)

<div class="container gutter gutter-lg gutter-top">
    <div class="grid gutter gutter-lg gutter-top">
        <div class="grid-lg-12">
            <div class="notice info">
                <i class="fa fa-info-circle"></i> Inga sökträffar…
            </div>
        </div>
    </div>
</div>

@else

<section>
    <div class="container">
        <div class="grid">
            <div class="grid-md-12 grid-lg-9">
                @if ($resultCount > 0)
                <div class="grid">
                    <div class="grid-lg-12">
                        {!!
                            paginate_links(array(
                                'type' => 'list'
                            ))
                        !!}
                    </div>
                </div>
                @endif

                <div class="grid">
                    <div class="grid-lg-12">
                        <ul class="search-result-list">

                            @while(have_posts())
                                {!! the_post() !!}
                                <li>
                                    <div class="search-result-item">
                                        <span class="search-result-date">{{ the_modified_date() }}</span>
                                        <h3><a href="{{ get_permalink() }}">{{ the_title() }}</a></h3>
                                        <p>{{ the_excerpt() }}</p>
                                        <div class="search-result-info">
                                            <span class="search-result-url"><i class="fa fa-globe"></i> <a href="{{ get_permalink() }}">{{ get_permalink() }}</a></span>
                                        </div>
                                    </div>
                                </li>
                            @endwhile

                        </ul>
                    </div>
                </div>

                @if ($resultCount > 0)
                <div class="grid">
                    <div class="grid-lg-12">
                        {!!
                            paginate_links(array(
                                'type' => 'list'
                            ))
                        !!}
                    </div>
                </div>
                @endif
            </div>

            @include('partials.sidebar-right')
        </div>
    </div>
</section>

@endif
