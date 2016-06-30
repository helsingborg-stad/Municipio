@extends('templates.master')

@section('content')

<header class="profile-header">
    <div class="profile-header-background" style="background-image:url('{{ !empty(get_the_author_meta('user_profile_picture')) ? get_the_author_meta('user_profile_picture') : 'http://www.helsingborg.se/wp-content/uploads/2016/05/varen_2016_2_1800x350.jpg' }}');"></div>

    <div class="container">
        <div class="grid">
            <div class="grid-xs-12">
                <div class="profile-header-content">
                    @if (!empty(get_the_author_meta('user_profile_picture')))
                    <div class="profile-image" style="background-image:url('{{ get_the_author_meta('user_profile_picture') }}');"></div>
                    @endif

                    <div class="profile-basics">
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
                    </div>

                    @if (get_current_user_id() == get_the_author_meta('ID') || is_super_admin())
                    <ul class="profile-actions">
                        <li><a href="{{ municipio_intranet_get_user_profile_edit_url(get_the_author_meta('user_login')) }}" class="btn btn-primary"><i class="fa fa-wrench"></i> <?php _e('Edit settings', 'municipio-intranet'); ?></a></li>
                    </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
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
                <div class="grid-xs-12">
                    <div class="box box-filled">
                        <h4 class="box-title"><?php _e('Contact information', 'municipio-intranet'); ?></h4>
                        <div class="box-content">
                            <p>
                                <strong><?php _e('Email', 'municipio-intranet'); ?></strong><br>
                                <a href="mailto:{{ get_the_author_meta('email') }}">{{ get_the_author_meta('email') }}</a>
                            </p>
                            <p>
                                <strong><?php _e('Phone number', 'municipio-intranet'); ?></strong><br>
                                @if (!empty(get_the_author_meta('user_phone')))
                                <a href="tel:{{ get_the_author_meta('user_phone') }}">{{ get_the_author_meta('user_phone') }}</a>
                                @else
                                <?php _e('No phone number given', 'municipio-intranet'); ?>
                                @endif
                            </p>
                            <p>
                                <strong><?php _e('Office', 'municipio-intranet'); ?></strong><br>
                                Kontaktcenter
                            </p>
                        </div>
                    </div>
                </div>
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
            </div>
        </aside>
    </div>
</div>

@stop
