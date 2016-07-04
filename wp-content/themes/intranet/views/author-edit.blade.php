@extends('templates.master')

@section('content')

<form action="" method="post">
{!! wp_nonce_field('user_settings_update_' . $user->ID) !!}

<div class="container main-container">
    <div class="grid">
        <div class="grid-lg-9 grid-md-12">
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
                                <h4><?php _e('Personal information', 'municipio-intranet'); ?></h4>
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
                                            <input type="tel" id="user_phone" name="user_phone" value="{{ get_the_author_meta('user_phone') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- About me -->
                        <section class="accordion-section">
                            <input type="radio" name="active-section" id="user-about">
                            <label class="accordion-toggle" for="user-about">
                                <h4><?php _e('About me', 'municipio-intranet'); ?></h4>
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
                                <h4><?php _e('Work information', 'municipio-intranet'); ?></h4>
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
                                                <div class="grid-md-6">
                                                    <label class="checkbox">
                                                        <input type="radio" name="user_administration_unit" value="" {{ checked('', get_the_author_meta('user_administration_unit')) }}>
                                                        <?php _e('N/A', 'municipio-intranet'); ?>
                                                    </label>
                                                </div>
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
                            </div>
                        </section>

                        <!-- Responsibilities -->
                        <section class="accordion-section">
                            <input type="radio" name="active-section" id="work-responsibilities">
                            <label class="accordion-toggle" for="work-responsibilities">
                                <h4><?php _e('Area of responsibility', 'municipio-intranet'); ?></h4>
                            </label>
                            <div class="accordion-content">
                                <div class="grid">
                                    <div class="grid-md-12">
                                        <p><?php _e('Please add your areas of responsibility. This will make it easier to find you when searching.', 'municipio-intranet'); ?></p>
                                    </div>
                                </div>

                                <div class="grid">
                                    <div class="grid-md-12">
                                        <div class="form-group">
                                            <label for="responsibility-autocomplete"><?php _e('Responsibility', 'municipio-intranet'); ?></label>
                                            <div class="tag-manager" data-input-name="responsibilities" data-wp-ajax-action="autocomplete_responsibilities">
                                                <div class="tag-manager-input">
                                                    <div class="input-group">
                                                        <input type="text" name="tag" class="form-control" placeholder="<?php _e('Add area of responsibility', 'municipio-intranet'); ?>…" autocomplete="off">
                                                        <span class="input-group-addon-btn"><button name="add-tag" class="btn"><?php _e('Add', 'municipio-intranet'); ?></button></span>
                                                    </div>
                                                </div>
                                                <div class="tag-manager-selected">
                                                    <label class="label-sm"><?php _e('Added responsibilities', 'municipio-intranet'); ?></label>
                                                    <ul class="tags">
                                                        @foreach ($userResponsibilities as $item)
                                                        <li>
                                                            <button class="btn btn-plain" data-action="remove">&times;</button>
                                                            {{ $item }}
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
                                <h4><?php _e('Skills', 'municipio-intranet'); ?></h4>
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
                                                            <button class="btn btn-plain" data-action="remove">&times;</button>
                                                            {{ $item }}
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
                                <h4><?php _e('Profile image', 'municipio-intranet'); ?></h4>
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

                                                <div class="image-upload inline-block" data-max-files="1" data-max-size="500" data-preview-image="true" style="width:250px;height:250px;">
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
                                <h4><?php _e('Target groups', 'municipio-intranet'); ?></h4>
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
                                <h4><?php _e('Theme', 'municipio-intranet'); ?></h4>
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
            </div>
        </aside>
    </div>
</div>
</form>

@stop
