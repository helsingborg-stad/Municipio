@extends('templates.master')

@section('content')

<header class="profile-header">
    <div class="profile-header-background hidden" style="background-image:url('{{ !empty(get_the_author_meta('user_profile_picture')) ? get_the_author_meta('user_profile_picture') : 'http://www.helsingborg.se/wp-content/uploads/2016/05/varen_2016_2_1800x350.jpg' }}');"></div>

    <div class="container">
        <div class="grid">
            <div class="grid-xs-12">
                <div class="profile-header-content">
                    @if (!empty(get_the_author_meta('user_profile_picture')))
                    <div class="profile-image" style="background-image:url('{{ get_the_author_meta('user_profile_picture') }}');"></div>
                    @endif

                    <h1 class="profile-fullname">{{ municipio_intranet_get_user_full_name(get_the_author_meta('ID')) }}</h1>
                    @if (!empty(get_the_author_meta('ad_title')))
                         <span class="profile-title">{{ get_the_author_meta('ad_title') }}</span>
                    @elseif (!empty(get_the_author_meta('user_work_title')))
                        <span class="profile-title">{{ get_the_author_meta('user_work_title') }}</span>
                    @endif

                    @if (!empty(get_the_author_meta('user_administration_unit')) || !empty(get_the_author_meta('user_department')))
                        <span class="profile-department">
                            {{ !empty(get_the_author_meta('user_administration_unit')) ? municipio_intranet_get_administration_unit_name(get_the_author_meta('user_administration_unit')) : '' }}{{ !empty(get_the_author_meta('user_administration_unit')) && !empty(get_the_author_meta('user_department')) ? ',' : '' }}
                            {{ !empty(get_the_author_meta('user_department')) ? get_the_author_meta('user_department') : '' }}
                        </span>
                    @endif

                    <ul class="profile-social-networks nav-horizontal">
                        <li><a href="#" data-tooltip="<?php _e('My profile on Facebook', 'municipio-intranet'); ?>"><i class="fa fa-facebook"></i><span class="sr-only"><?php _e('My profile on Facebook', 'municipio-intranet'); ?></span></a></li>
                        <li><a href="#" data-tooltip="<?php _e('My profile on Instagram', 'municipio-intranet'); ?>"><i class="fa fa-instagram"><span class="sr-only"><?php _e('My profile on Instagram', 'municipio-intranet'); ?></span></i></a></li>
                        <li><a href="#" data-tooltip="<?php _e('My profile on Twitter', 'municipio-intranet'); ?>"><i class="fa fa-twitter"></i><span class="sr-only"><?php _e('My profile on Twitter', 'municipio-intranet'); ?></span></a></li>
                        <li><a href="#" data-tooltip="<?php _e('My profile on Snapchat', 'municipio-intranet'); ?>"><i class="fa fa-snapchat"></i><span class="sr-only"><?php _e('My profile on Snapchat', 'municipio-intranet'); ?></span></a></li>
                    </ul>

                    @if (get_current_user_id() == get_the_author_meta('ID') || is_super_admin())
                    <ul class="profile-actions">
                        <li><a href="{{ municipio_intranet_get_user_profile_edit_url(get_the_author_meta('user_login')) }}"><i class="fa fa-wrench"></i> <?php _e('Edit settings', 'municipio-intranet'); ?></a></li>
                    </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <ul class="profile-contact nav-horizontal">
        @if (!empty(get_the_author_meta('user_phone')))
        <li>
            <a href="tel:{{ get_the_author_meta('user_phone') }}">
                <i class="fa fa-phone fa-2x"></i>
                <span class="value">
                    {{ get_the_author_meta('user_phone') }}
                    <span class="value-label"><i class="fa fa-phone"></i> <?php _e('Phone number', 'municipio-intranet'); ?></span>
                </span>
            </a>
        </li>
        @endif

        @if (!empty(get_the_author_meta('user_phone')))
        <li>
            <a href="mailto:{{ get_the_author_meta('email') }}">
                <i class="fa fa-envelope-o fa-2x"></i>
                <span class="value">
                    {{ get_the_author_meta('email') }}
                    <span class="value-label"><i class="fa fa-envelope-o"></i> <?php _e('Email', 'municipio-intranet'); ?></span>
                </span>
            </a>
        </li>
        @endif

        <li>
            <a href="mailto:{{ get_the_author_meta('email') }}">
                <i class="fa fa-map-marker fa-2x"></i>
                <span class="value">
                    Kontaktcenter
                    <span class="value-label"><i class="fa fa-map-marker"></i> <?php _e('Office', 'municipio-intranet'); ?></span>
                </span>
            </a>
        </li>
    </ul>
</header>

<div class="container main-container">
    <div class="grid">
        <div class="grid-md-8">
            <article>
                <h2><?php _e('About', 'municipio-intranet'); ?></h2>
                {!! wpautop(get_the_author_meta('user_about')) !!}
            </article>
        </div>

        <aside class="grid-md-4 sidebar-right-sidebar">
            <div class="grid">
                @if (count($userSkills) > 0)
                <div class="grid-xs-12">
                    <div class="box box-filled">
                        <h4 class="box-title"><?php _e('Skills', 'municipio-intranet'); ?></h4>
                        <div class="box-content">
                            <ul class="tags">
                                @foreach ($userSkills as $item)
                                <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endif

                @if (count($userResponsibilities) > 0)
                <div class="grid-xs-12">
                    <div class="box box-filled">
                        <h4 class="box-title"><?php _e('Responsibilities', 'municipio-intranet'); ?></h4>
                        <div class="box-content">
                            <ul class="tags">
                                @foreach ($userResponsibilities as $item)
                                <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </aside>
    </div>
</div>

@stop
