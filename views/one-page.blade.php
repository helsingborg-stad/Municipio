@extends('templates.master')
@section('content')

    <div class="container main-container">
        @if (is_singular() && comments_open() && get_option('comment_registration') == 0 || is_singular() && comments_open() && is_user_logged_in())
            @if(get_option('comment_order') == 'desc')
                <div class="grid">
                    <div class="grid-sm-12">
                        @include('partials.blog.comments-form')
                    </div>
                </div>
                @if(isset($comments) && ! empty($comments))
                    <div class="grid">
                        <div class="grid-sm-12">
                            @include('partials.blog.comments')
                        </div>
                    </div>
                @endif
            @else
                @if(isset($comments) && ! empty($comments))
                    <div class="grid">
                        <div class="grid-sm-12">
                            @include('partials.blog.comments')
                        </div>
                    </div>
                @endif
                <div class="grid">
                    <div class="grid-sm-12">
                        @include('partials.blog.comments-form')
                    </div>
                </div>
            @endif
        @endif
    </div>

@stop