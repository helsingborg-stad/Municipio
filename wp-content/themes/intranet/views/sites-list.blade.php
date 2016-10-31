@extends('templates.master')

@section('content')

<div class="container main-container">

    <div class="grid">
        <div class="grid-xs-12">
            <article>
                <h1><?php _e('All intranets', 'municipio-intranet'); ?></h1>
                <p>
                    <?php _e('This is a list of available intranets. You can follow or unfollow them by clicking the follow/unfollow buttons. Intranets missing the follow/unfollow buttons are marked as mandatory and can therefor not be unfollowed.', 'municipio-intranet'); ?>
                </p>
            </article>
        </div>
    </div>

    <div class="grid" data-equal-container>
        @foreach (\Intranet\Helper\Multisite::getSitesList(true) as $site)
            <div class="grid-md-4">
                <div class="box box-index" data-equal-item>
                    <div class="box-content">
                        <h5 class="box-index-title link-item"><a href="{{ $site->path }}">{!! municipio_intranet_format_site_name($site) !!}</a></h5>
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
                </div>
            </div>
        @endforeach
    </div>
</div>

@stop
