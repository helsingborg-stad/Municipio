@extends('templates.master')

@section('content')

<header class="profile-header">
    <div class="profile-header-background" style="background-image:url('{{ !empty(get_the_author_meta('user_profile_image')) ? get_the_author_meta('user_profile_image') : 'http://www.helsingborg.se/wp-content/uploads/2016/05/varen_2016_2_1800x350.jpg' }}');"></div>

    <div class="container">
        <div class="grid">
            <div class="grid-xs-12">
                <div class="profile-header-content">
                    @if (!empty(get_the_author_meta('user_profile_image')))
                    <div class="profile-image" style="background-image:url('{{ get_the_author_meta('user_profile_image') }}');"></div>
                    @endif

                    <div class="profile-basics">
                        <h1 class="profile-fullname">{{ get_the_author_meta('first_name') . ' ' . get_the_author_meta('last_name') }}</h1>

                        @if (!empty(get_the_author_meta('user_work_title')))
                            <span class="profile-title">{{ get_the_author_meta('user_work_title') }}</span>
                        @endif

                        @if (!empty(get_the_author_meta('user_administration_unit')) || !empty(get_the_author_meta('user_department')))
                            <span class="profile-department">
                                {{ !empty(get_the_author_meta('user_administration_unit')) ? get_the_author_meta('user_administration_unit') : '' }}{{ !empty(get_the_author_meta('user_administration_unit')) && !empty(get_the_author_meta('user_department')) ? ',' : '' }}
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
                <p>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque ultricies aliquam dolor et tristique.
                    Aenean nec velit vel sapien scelerisque luctus in quis erat. In lacinia massa vitae congue scelerisque.
                    Phasellus ultricies vehicula ultrices. Maecenas a velit ligula. Maecenas vitae massa eget mi dapibus fermentum.
                    In nec magna eros. Fusce nec semper libero, bibendum rhoncus dui. Mauris a ante eget felis porttitor aliquam id in orci.
                </p>

                <p>
                    Fusce eget augue eget felis facilisis aliquam quis id odio. Aenean aliquam consectetur ipsum quis lobortis.
                    Proin finibus a sem ac tincidunt. Cras sed imperdiet elit. Integer accumsan purus ut eros consectetur, nec congue quam posuere.
                    Cras hendrerit risus odio, porta malesuada nibh elementum vel. Praesent commodo ex in congue tristique.
                </p>
            </article>
        </div>

        <div class="grid-md-4">
            <div class="grid">
                <div class="grid-xs-12">
                    <div class="gutter gutter-bottom">
                        <div class="notice warning"><i class="fa fa-warning"></i> {{ get_the_author_meta('first_name') }} är pappaledig till 2016-06-14</div>
                    </div>
                </div>
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
            </div>
        </div>
    </div>
</div>

@stop
