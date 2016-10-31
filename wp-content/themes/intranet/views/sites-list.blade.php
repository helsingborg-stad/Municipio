@extends('templates.master')

@section('content')

<div class="container main-container">

    <div class="grid" data-equal-container>
        @foreach (\Intranet\Helper\Multisite::getSitesList(true) as $site)
            <div class="grid-md-4">
                <a href="{{ $site->path }}" class="box box-index" data-equal-item>
                    <div class="box-content">
                        <h5 class="box-index-title link-item">{!! municipio_intranet_format_site_name($site) !!}</h5>
                        @if (!empty($site->description))
                        <p>{{ $site->description }}</p>
                        @endif

                        @if (is_user_logged_in() && !is_author() && get_blog_option($site->blog_id, 'intranet_force_subscription') != 'true')
                        <p>
                            <button class="btn btn-primary btn-subscribe" data-subscribe="{{ $site->blog_id }}">
                                @if (!\Intranet\User\Subscription::hasSubscribed($site->blog_id))
                                <i class="pricon pricon-plus-o"></i> <?php _e('Follow', 'municipio-intranet'); ?>
                                @else
                                <i class="pricon pricon-minus-o"></i> <?php _e('Unfollow', 'municipio-intranet'); ?>
                                @endif
                            </button>
                        </p>
                        @endif
                    </div>
                </a>
            </div>
        @endforeach
    </div>
</div>

@stop
