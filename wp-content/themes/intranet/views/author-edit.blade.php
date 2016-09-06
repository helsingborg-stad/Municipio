@extends('templates.master')

@section('content')

<form action="" method="post">
{!! wp_nonce_field('user_settings_update_' . $user->ID) !!}

<div class="container main-container">
    <div class="grid">
        <div class="grid-lg-9 grid-md-12">
            @if (isset($_POST['_wpnonce']))
            <div class="grid">
                <div class="grid-xs-12">
                    <div class="notice success">
                        <i class="pricon pricon-check"></i> <?php _e('Settings saved', 'municipio-intranet'); ?>
                    </div>
                </div>
            </div>
            @endif

            <div class="grid">
                <div class="grid-xs-12">
                    <article>
                        @if ($currentUser->ID === $user->ID)
                        <h1><?php _e('Your settings', 'municipio-intranet'); ?></h1>
                        @else
                        <h1><?php echo sprintf(__('Settings of %s', 'municipio-intranet'), municipio_intranet_get_user_full_name($user->ID)) ; ?></h1>
                        @endif

                        <p>
                            <?php _e('Click on a section to view and edit your settings.', 'municipio-intranet'); ?>
                        </p>
                    </article>
                </div>
            </div>

            <div class="grid">
                <div class="grid-xs-12">
                    <div class="accordion accordion-icon accordion-list">

                        <!-- Personal information -->
                        <section class="accordion-section">
                            <input type="radio" name="active-section" id="personal-information">
                            <label class="accordion-toggle" for="personal-information">
                                <h4 class="pricon pricon-user pricon-space-right"><?php _e('Personal information', 'municipio-intranet'); ?></h4>
                            </label>
                            <div class="accordion-content">
                                <div class="grid">
                                    <div class="grid-md-6">
                                        <div class="form-group">
                                            <label for="user_first_name"><?php _e('First name', 'municipio-intranet'); ?> <small>(<?php _e('Not editable', 'municipio-intranet'); ?>)</small></label>
                                            <input type="text" id="user_first_name" name="user_first_name" value="{{ get_the_author_meta('first_name') }}" disabled>
                                        </div>
                                    </div>
                                    <div class="grid-md-6">
                                        <div class="form-group">
                                            <label for="user_last_name"><?php _e('Last name', 'municipio-intranet'); ?> <small>(<?php _e('Not editable', 'municipio-intranet'); ?>)</small></label>
                                            <input type="text" id="user_last_name" name="user_last_name" value="{{ get_the_author_meta('last_name') }}" disabled>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid">
                                    <div class="grid-md-6">
                                        <div class="form-group">
                                            <label for="user_email"><?php _e('Email', 'municipio-intranet'); ?> <small>(<?php _e('Not editable', 'municipio-intranet'); ?>)</small></label>
                                            <input type="email" id="user_email" name="user_email" value="{{ get_the_author_meta('email') }}" disabled>
                                        </div>
                                    </div>
                                    <div class="grid-md-6">
                                        <div class="form-group">
                                            <label for="user_phone"><?php _e('Phone number', 'municipio-intranet'); ?></label>
                                            <input type="tel" id="user_phone" name="user_phone" value="{{ get_the_author_meta('user_phone') }}" pattern="^\+?([\d|\s|(|)|\-])+" oninvalid="this.setCustomValidity('<?php _e('The phone number supplied is invalid', 'municipio-intranet'); ?>')">
                                        </div>
                                    </div>
                                </div>

                                <div class="grid">
                                    <div class="grid-md-6">
                                        <div class="form-group">
                                            <label for="user_hometown"><?php _e('Hometown', 'municipio-intranet'); ?></label>
                                            <input type="text" id="user_hometown" name="user_hometown" value="{{ get_the_author_meta('user_hometown') }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="grid">
                                    <div class="grid-md-12">
                                        <div class="form-group">
                                            <label><?php _e('Date of birth', 'municipio-intranet'); ?></label>
                                        </div>
                                    </div>


                                    <div class="grid-md-4">
                                        <div class="form-group">
                                            <label for="user_birthday_year" style="font-weight:normal;"><?php _e('Year', 'municipio-intranet'); ?></label>
                                            <select name="user_birthday[year]" id="user_birthday_year">
                                                <option value="" default><?php _e('Select year…', 'municipio-intranet'); ?></option>
                                                @for ($i = date('Y') - 13; $i >= date('Y') - 100; $i--)
                                                    <option value="{{ $i }}" {{ isset(get_user_meta(get_current_user_id(), 'user_birthday', true)['year']) ? selected(get_user_meta(get_current_user_id(), 'user_birthday', true)['year'], $i) : null }}>{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>

                                    <div class="grid-md-4">
                                        <div class="form-group">
                                            <label for="user_birthday_month" style="font-weight:normal;"><?php _e('Month', 'municipio-intranet'); ?></label>
                                            <select name="user_birthday[month]" id="user_birthday_month">
                                                <option value="" default><?php _e('Select month…', 'municipio-intranet'); ?></option>
                                                @for ($i = 1; $i <= 12; $i++)
                                                    <option value="{{ $i }}" {{ isset(get_user_meta(get_current_user_id(), 'user_birthday', true)['month']) ? selected(get_user_meta(get_current_user_id(), 'user_birthday', true)['month'], $i) : null }}>{{ ucfirst(mysql2date('F', date('Y') . '-' . $i . '-01')) }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>

                                    <div class="grid-md-4">
                                        <div class="form-group">
                                            <label for="user_birthday_day" style="font-weight:normal;"><?php _e('Day', 'municipio-intranet'); ?></label>
                                            <select name="user_birthday[day]" id="user_birthday_day">
                                                <option value="" default><?php _e('Select day…', 'municipio-intranet'); ?></option>
                                                @for ($i = 1; $i <= 31; $i++)
                                                    <option value="{{ $i }}" {{ isset(get_user_meta(get_current_user_id(), 'user_birthday', true)['day']) ? selected(get_user_meta(get_current_user_id(), 'user_birthday', true)['day'], $i) : null }}>{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid">
                                    <div class="grid-md-12">
                                        <div class="form-group">
                                            <label class="checkbox">
                                                <input type="checkbox" name="user_hide_birthday" value="1" {{ checked(get_user_meta(get_current_user_id(), 'user_hide_birthday', true), true) }}>
                                                <?php _e('Keep my date of birth secret', 'municipio-intranet'); ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Social media -->
                        <section class="accordion-section">
                            <input type="radio" name="active-section" id="social-media">
                            <label class="accordion-toggle" for="social-media">
                                <h4 class="pricon pricon-share pricon-space-right"><?php _e('Social media', 'municipio-intranet'); ?></h4>
                            </label>
                            <div class="accordion-content">
                                <div class="grid">
                                    <div class="grid-md-6">
                                        <div class="form-group">
                                            <label for="user_facebook_url"><?php _e('Facebook profile url', 'municipio-intranet'); ?></label>
                                            <input type="url" pattern="https?://.+" id="user_facebook_url" name="user_facebook_url" value="{{ get_the_author_meta('user_facebook_url') }}" oninvalid="this.setCustomValidity('<?php _e('The given url is invalid. Make sure the url starts with https:// or http://', 'municipio-intranet'); ?>')">
                                        </div>
                                    </div>
                                    <div class="grid-md-6">
                                        <div class="form-group">
                                            <label for="user_linkedin_url"><?php _e('Linkedin profile url', 'municipio-intranet'); ?></label>
                                            <input type="url" pattern="https?://.+" id="user_linkedin_url" name="user_linkedin_url" value="{{ get_the_author_meta('user_linkedin_url') }}" oninvalid="this.setCustomValidity('<?php _e('The given url is invalid. Make sure the url starts with https:// or http://', 'municipio-intranet'); ?>')">
                                        </div>
                                    </div>
                                </div>

                                <div class="grid">
                                    <div class="grid-md-6">
                                        <div class="form-group">
                                            <label for="user_instagram_username"><?php _e('Instagram username', 'municipio-intranet'); ?></label>
                                            <input type="text" id="user_instagram_username" name="user_instagram_username" value="{{ get_the_author_meta('user_instagram_username') }}">
                                        </div>
                                    </div>
                                    <div class="grid-md-6">
                                        <div class="form-group">
                                            <label for="user_twitter_username"><?php _e('Twitter username', 'municipio-intranet'); ?></label>
                                            <input type="text" id="user_twitter_username" name="user_twitter_username" value="{{ get_the_author_meta('user_twitter_username') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- About me -->
                        <section class="accordion-section">
                            <input type="radio" name="active-section" id="user-about">
                            <label class="accordion-toggle" for="user-about">
                                <h4  class="pricon pricon-info-o pricon-space-right"><?php _e('About me', 'municipio-intranet'); ?></h4>
                            </label>
                            <div class="accordion-content no-padding">
                                <div class="grid">
                                    <div class="grid-xs-12">
                                        <div class="form-group">
                                            <label for="user_about" class="sr-only"><?php _e('About me', 'municipio-intranet'); ?></label>
                                            <textarea name="user_about" id="user_about" cols="30" rows="10" style="border:none;display: block;box-shadow: none;padding: 20px;" placeholder="<?php _e('Write a little something about yourself…', 'municipio-intranet'); ?>">{{ get_the_author_meta('user_about') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Work information -->
                        <section class="accordion-section">
                            <input type="radio" name="active-section" id="work-information">
                            <label class="accordion-toggle" for="work-information">
                                <h4  class="pricon pricon-breifcase pricon-space-right"><?php _e('Work information', 'municipio-intranet'); ?></h4>
                            </label>
                            <div class="accordion-content">
                                <div class="grid">
                                    <div class="grid-md-6">
                                        <div class="form-group">
                                            <label for="user_work_title"><?php _e('Work title', 'municipio-intranet'); ?><?php echo !empty(get_the_author_meta('ad_title')) ? ' <small>(' . __('Not editable', 'municipio-intranet') . ')</small>' : ''; ?></label>
                                            @if (!empty(get_the_author_meta('ad_title')))
                                            <input type="text" id="user_work_title" name="ad_title" value="{{ get_the_author_meta('ad_title') }}" disabled>
                                            @else
                                            <input type="text" id="user_work_title" name="user_work_title" value="{{ get_the_author_meta('user_work_title') }}">
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="grid">
                                    <div class="grid-md-12">
                                        <div class="form-group">
                                            <label for="user_administration_unit"><?php _e('Administration unit', 'municipio-intranet'); ?></label>
                                            <div class="grid">
                                                @foreach ($administrationUnits as $unit)
                                                <div class="grid-md-6">
                                                    <label class="checkbox">
                                                        <input type="radio" name="user_administration_unit" value="{{ $unit->id }}" {{ checked(true, in_array($unit->id, (array)get_the_author_meta('user_administration_unit'))) }}>
                                                        {{ $unit->name }}
                                                    </label>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid">
                                    <div class="grid-md-6">
                                        <div class="form-group">
                                            <label for="user_department"><?php _e('Department', 'municipio-intranet'); ?></label>
                                            <input type="text" id="user_department" name="user_department" value="{{ get_the_author_meta('user_department') }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="grid">
                                    <div class="grid-md-12">
                                        <div class="form-group">
                                            <label><?php _e('Visiting address', 'municipio-intranet'); ?></label>
                                        </div>
                                    </div>

                                    <div class="grid-md-4">
                                        <div class="form-group">
                                            <label for="user_visiting_address_place" style="font-weight:normal;"><?php _e('Workplace', 'municipio-intranet'); ?></label>
                                            <input type="text" id="user_visiting_address_place" name="user_visiting_address[workplace]" value="{{ isset(get_the_author_meta('user_visiting_address')['workplace']) ? get_the_author_meta('user_visiting_address')['workplace'] : '' }}">
                                        </div>
                                    </div>

                                    <div class="grid-md-4">
                                        <div class="form-group">
                                            <label for="user_visiting_address_street" style="font-weight:normal;"><?php _e('Street address', 'municipio-intranet'); ?></label>
                                            <input type="text" id="user_visiting_address_street" name="user_visiting_address[street]" value="{{ isset(get_the_author_meta('user_visiting_address')['street']) ? get_the_author_meta('user_visiting_address')['street'] : '' }}">
                                        </div>
                                    </div>

                                    <div class="grid-md-4">
                                        <div class="form-group">
                                            <label for="user_visiting_address_city" style="font-weight:normal;"><?php _e('City', 'municipio-intranet'); ?></label>
                                            <input type="text" id="user_visiting_address_city" name="user_visiting_address[city]" value="{{ isset(get_the_author_meta('user_visiting_address')['city']) ? get_the_author_meta('user_visiting_address')['city'] : '' }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Responsibilities -->
                        <section class="accordion-section">
                            <input type="radio" name="active-section" id="work-responsibilities">
                            <label class="accordion-toggle" for="work-responsibilities">
                                <h4 class="pricon pricon-clipboard pricon-space-right"><?php _e('Work assignments', 'municipio-intranet'); ?></h4>
                            </label>
                            <div class="accordion-content">
                                <div class="grid">
                                    <div class="grid-md-12">
                                        <p><?php _e('Please add your work assignments. This will make it easier to find you when searching.', 'municipio-intranet'); ?></p>
                                    </div>
                                </div>

                                <div class="grid">
                                    <div class="grid-md-12">
                                        <div class="form-group">
                                            <label for="responsibility-autocomplete"><?php _e('Work assignments', 'municipio-intranet'); ?></label>
                                            <div class="tag-manager" data-input-name="responsibilities" data-wp-ajax-action="autocomplete_responsibilities">
                                                <div class="tag-manager-input">
                                                    <div class="input-group">
                                                        <input type="text" name="tag" class="form-control" placeholder="<?php _e('Add area of responsibility', 'municipio-intranet'); ?>…" autocomplete="off">
                                                        <span class="input-group-addon-btn"><button name="add-tag" class="btn"><?php _e('Add', 'municipio-intranet'); ?></button></span>
                                                    </div>
                                                </div>
                                                <div class="tag-manager-selected">
                                                    <label class="label-sm"><?php _e('Added work assignments', 'municipio-intranet'); ?></label>
                                                    <ul class="tags">
                                                        @foreach ($userResponsibilities as $item)
                                                        <li>
                                                            <span class="tag">
                                                                <button class="btn btn-plain" data-action="remove">&times;</button>
                                                                {{ $item }}
                                                            </span>
                                                            <input type="hidden" name="responsibilities[]" value="{{ $item }}">
                                                        </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Skills -->
                        <section class="accordion-section">
                            <input type="radio" name="active-section" id="skills">
                            <label class="accordion-toggle" for="skills">
                                <h4 class="pricon pricon-lightbulb pricon-space-right"><?php _e('Skills', 'municipio-intranet'); ?></h4>
                            </label>
                            <div class="accordion-content">
                                <div class="grid">
                                    <div class="grid-md-12">
                                        <p><?php _e('Please add your skills. This will make it easier to find you when searching.', 'municipio-intranet'); ?></p>
                                    </div>
                                </div>

                                <div class="grid">
                                    <div class="grid-md-12">
                                        <div class="form-group">
                                            <label for="responsibility-autocomplete"><?php _e('Skill', 'municipio-intranet'); ?></label>
                                            <div class="tag-manager" data-input-name="skills" data-wp-ajax-action="autocomplete_skills">
                                                <div class="tag-manager-input">
                                                    <div class="input-group">
                                                        <input type="text" name="tag" class="form-control" placeholder="<?php _e('Add skill', 'municipio-intranet'); ?>…" autocomplete="off">
                                                        <span class="input-group-addon-btn"><button name="add-tag" class="btn"><?php _e('Add', 'municipio-intranet'); ?></button></span>
                                                    </div>
                                                </div>
                                                <div class="tag-manager-selected">
                                                    <label class="label-sm"><?php _e('Added skills', 'municipio-intranet'); ?></label>
                                                    <ul class="tags">
                                                        @foreach ($userSkills as $item)
                                                        <li>
                                                            <span class="tag">
                                                                <button class="btn btn-plain" data-action="remove">&times;</button>
                                                                {{ $item }}
                                                            </span>
                                                            <input type="hidden" name="skills[]" value="{{ $item }}">
                                                        </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Profile iamge -->
                        <section class="accordion-section">
                            <input type="radio" name="active-section" id="profile-image">
                            <label class="accordion-toggle" for="profile-image">
                                <h4 class="pricon pricon-picture-user pricon-space-right"><?php _e('Profile image', 'municipio-intranet'); ?></h4>
                            </label>
                            <div class="accordion-content">
                                <div class="grid">
                                    <div class="grid-md-12">
                                        <div class="form-group">
                                            <div class="profile-image-upload">
                                                @if (!empty(get_the_author_meta('user_profile_picture')))
                                                <div class="profile-image profile-image-250 inline-block" style="background-image:url('{{ get_the_author_meta('user_profile_picture') }}');">
                                                    <button onclick="return confirm('<?php _e('Are your sure you want to remove the profile image?', 'municipio-intranet'); ?>');" formaction="?remove_profile_image=true" class="btn btn-icon btn-danger btn-sm text-lg" data-tooltip="<?php _e('Delete profile image', 'municipio-intranet'); ?>" data-tooltip-right>&times;</button>
                                                </div>
                                                @endif

                                                <div class="image-upload inline-block" data-max-files="1" data-max-size="2000" data-preview-image="true" style="width:250px;height:250px;">
                                                    <div class="placeholder">
                                                        <span class="fa-stack fa-2x">
                                                            <i class="fa fa-picture-o fa-stack-2x"></i>
                                                            <i class="fa fa-plus-circle fa-stack-1x"></i>
                                                        </span>
                                                        <div class="placeholder-text">
                                                            <span class="placeholder-text-drag"><?php _e('Drag a photo here', 'municipio-intranet'); ?></span>
                                                            <span class="placeholder-text-browse">
                                                                <em class="placeholder-text-or"><?php _e('or', 'municipio-intranet'); ?></em>
                                                                <label for="user_profile_image" class="btn btn-secondary btn-select-file"><?php _e('Select a photo', 'municipio-intranet'); ?></label>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="placeholder placeholder-is-dragover">
                                                        <span>Drop it like it's hot</span>
                                                    </div>
                                                    <div class="selected-file"></div>
                                                    <input type="file" id="user_profile_image" name="user_profile_image" class="hidden">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Traget groups -->
                        <section class="accordion-section">
                            <input type="radio" name="active-section" id="target-groups">
                            <label class="accordion-toggle" for="target-groups">
                                <h4 class="pricon pricon-target pricon-space-right"><?php _e('Target groups', 'municipio-intranet'); ?></h4>
                            </label>
                            <div class="accordion-content">
                                <div class="grid">
                                    <div class="grid-md-12">
                                        <div class="form-group">
                                            <div class="grid">
                                                @foreach ($targetGroups as $group)
                                                <div class="grid-md-6">
                                                    <label class="checkbox"><input type="checkbox" name="user_target_groups[]" value="{{ $group->id }}" {{ checked(true, in_array($group->id, (array)get_the_author_meta('user_target_groups'))) }}> {{ $group->tag }}</label>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Intranet personalization -->
                        <section class="accordion-section">
                            <input type="radio" name="active-section" id="user-personalization">
                            <label class="accordion-toggle" for="user-personalization">
                                <h4 class="pricon pricon-paintbrush pricon-space-right"><?php _e('Theme', 'municipio-intranet'); ?></h4>
                            </label>
                            <div class="accordion-content">
                                <div class="grid">
                                    <div class="grid-xs-12">
                                        <div class="form-group">
                                            <label><?php _e('Color scheme', 'municipio-intranet'); ?></label>
                                            <label class="checkbox"><input type="radio" name="color_scheme" value="red" {{ checked(get_the_author_meta('user_color_scheme'), 'red') }}> <?php _e('Red', 'municipio-intranet'); ?></label>
                                            <label class="checkbox"><input type="radio" name="color_scheme" value="blue" {{ checked(get_the_author_meta('user_color_scheme'), 'blue') }}> <?php _e('Blue', 'municipio-intranet'); ?></label>
                                            <label class="checkbox"><input type="radio" name="color_scheme" value="green" {{ checked(get_the_author_meta('user_color_scheme'), 'green') }}> <?php _e('Green', 'municipio-intranet'); ?></label>
                                            <label class="checkbox"><input type="radio" name="color_scheme" value="purple" {{ checked(get_the_author_meta('user_color_scheme') == 'purple' || empty(get_the_author_meta('user_color_scheme')), true) }}> <?php _e('Purple', 'municipio-intranet'); ?></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>

            <div class="grid">
                <div class="grid-xs-12">
                    <div class="form-group">
                        <input class="btn btn-primary" type="submit" value="<?php _e('Save settings', 'municipio-intranet'); ?>">
                    </div>
                </div>
            </div>
        </div>

        <aside class="grid-lg-3 grid-md-12 sidebar-right-sidebar">
            <div class="grid">
                <div class="grid-xs-12">
                    <div class="box box-filled">
                        <h4 class="box-title"><?php _e('Edit your settings', 'municipio-intranet'); ?></h4>
                        <div class="box-content">
                            <p><?php _e('This is where you edit your personal settings.', 'municipio-intranet'); ?></p>
                            <p><?php _e('Some of your settings is displayed on your profile to other logged in users.', 'municipio-intranet'); ?></p>
                        </div>
                    </div>
                </div>

                <div class="grid-xs-12">
                    <div class="box box-filled">
                        <h4 class="box-title"><?php _e('Sync from Facebook', 'municipio-intranet'); ?></h4>
                        <div class="box-content">
                            <p>
                                <?php _e('We can sync some of your profile settings from Facebook. If you would like to do this click the login button below to login to Facebook and allow us to get needed information.', 'municipio-intranet'); ?>
                            </p>
                            <p>
                                <div class="fb-login-button" data-max-rows="1" data-size="large" data-show-faces="false" data-auto-logout-link="false" data-default-audience="only_me" data-scope="public_profile, user_about_me, user_birthday, user_hometown, user_location" onlogin="facebookLoginDone"></div>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</div>
</form>

@stop
