@extends('templates.error')

@section('content')

<div class="container main-container">
    <div class="grid">
        <div class="grid-lg-6 grid-md-8 grid-sm-12">
            <h1>404 <em>{{ get_field('404_error_message', 'option') ? get_field('404_error_message', 'option') : 'The page could not be found' }}</em></h1>

            <ul class="actions">
                @if (is_array(get_field('404_display', 'option')) && in_array('search', get_field('404_display', 'option')))
                <li>
                    <a rel="nofollow" href="{{ home_url() }}?s={{ $keyword }}" class="link-item link-item-light">{{ sprintf(get_field('404_display', 'option') ? get_field('404_search_link_text', 'option') : 'Search "%s"', $keyword) }}</a>
                </li>
                @endif

                @if (is_array(get_field('404_display', 'option')) && in_array('home', get_field('404_display', 'option')))
                <li><a href="{{ home_url() }}" class="link-item link-item-light">{{ get_field('404_home_link_text', 'option') ? get_field('404_home_link_text', 'option') : 'Go to home' }}</a></li>
                @endif
            </ul>

            {!! get_field('404_error_info', 'option') ? get_field('404_error_info', 'option') : '' !!}

            @if (is_array(get_field('404_display', 'option')) && in_array('back', get_field('404_display', 'option')))
            <p>
                <a href="javascript:history.go(-1);" class="btn btn-primary">
                    <i class="fa fa-arrow-circle-o-left"></i>
                    {{ get_field('404_back_button_text', 'option') ? get_field('404_back_button_text', 'option') : 'Go back' }}
                </a>
            </p>
            @endif
        </div>
    </div>
</div>

@stop
