@extends('templates.master')

@section('content')

<div class="container main-container">
    <div class="grid">
        <div class="grid-lg-9 grid-md-12">
            <div class="grid">
                <div class="grid-xs-12">
                    @if ($currentUser->ID === $user->ID)
                    <h1><?php _e('Manage your subscriptions', 'municipio-intranet'); ?></h1>
                    @else
                    <h1><?php echo sprintf(__('Manage subscriptions of %s', 'municipio-intranet'), municipio_intranet_get_user_full_name($user->ID)) ; ?></h1>
                    @endif
                </div>
            </div>

            <div class="grid">
                <div class="grid-xs-12">
                    <form action="" method="post">
                        {!! wp_nonce_field('user_subscriptions_update_' . $user->ID) !!}

                        <div class="grid">
                            <div class="grid-md-12">
                                <div class="form-group">
                                    <div class="grid">
                                        @foreach ($sites as $site)
                                        <div class="grid-md-6">
                                            <label class="checkbox"><input type="checkbox" name="user_subscriptions[]" value="{{ $site->blog_id }}" {{ checked(true, $site->subscribed) }}> {!! municipio_intranet_format_site_name($site) !!}</label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid">
                            <div class="grid-xs-12">
                                <div class="form-group">
                                    <input class="btn btn-primary" type="submit" value="<?php _e('Save subscriptions', 'municipio-intranet'); ?>">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <aside class="grid-lg-3 grid-md-12 sidebar-right-sidebar">

        </aside>
    </div>
</div>

@stop
