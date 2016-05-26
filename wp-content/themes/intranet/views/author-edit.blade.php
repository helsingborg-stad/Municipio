@extends('templates.master')

@section('content')

<div class="container main-container">
    <div class="grid">
        <div class="grid-lg-9 grid-md-12">
            <div class="grid">
                <div class="grid-xs-12">
                    @if ($currentUser->ID === $user->ID)
                    <h1><?php _e('Your settings', 'municipio-intranet'); ?></h1>
                    @else
                    <h1><?php echo sprintf(__('Settings of %s', 'municipio-intranet'), $user->first_name . ' ' . $user->last_name) ; ?></h1>
                    @endif
                </div>
            </div>

            <div class="grid">
                <div class="grid-xs-12">
                    <form action="" method="post">
                        {!! wp_nonce_field('user_settings_update_' . $user->ID) !!}

                        <div class="grid">
                            <div class="grid-md-6">
                                <div class="form-group">
                                    <label for="user_first_name"><?php _e('First name', 'municipio-intranet'); ?> <small>(<?php _e('Not editable', 'municipio-intranet'); ?>)</small></label>
                                    <input type="email" id="user_first_name" name="user_first_name" value="{{ get_the_author_meta('first_name') }}" disabled>
                                </div>
                            </div>
                            <div class="grid-md-6">
                                <div class="form-group">
                                    <label for="user_last_name"><?php _e('Last name', 'municipio-intranet'); ?> <small>(<?php _e('Not editable', 'municipio-intranet'); ?>)</small></label>
                                    <input type="email" id="user_last_name" name="user_last_name" value="{{ get_the_author_meta('last_name') }}" disabled>
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
                        </div>

                        <div class="grid">
                            <div class="grid-md-6">
                                <div class="form-group">
                                    <label for="user_work_title"><?php _e('Work title', 'municipio-intranet'); ?></label>
                                    <input type="text" id="user_work_title" name="user_work_title" value="{{ get_the_author_meta('user_work_title') }}">
                                </div>
                            </div>
                        </div>

                        <div class="grid">
                            <div class="grid-md-12">
                                <div class="form-group">
                                    <label for="user_administration_unit"><?php _e('Administration unit', 'municipio-intranet'); ?></label>
                                    <div class="grid">
                                        <div class="grid-md-6">
                                            <label class="checkbox"><input type="radio" name="user_administration_unit" value="" {{ checked('', get_the_author_meta('user_administration_unit')) }}> <?php _e('N/A', 'municipio-intranet'); ?></label>
                                        </div>
                                        @foreach ($administrationUnits as $unit)
                                        <div class="grid-md-6">
                                            <label class="checkbox"><input type="radio" name="user_administration_unit" value="{{ $unit }}" {{ checked($unit, get_the_author_meta('user_administration_unit')) }}> {{ $unit }}</label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid">
                            <div class="grid-md-12">
                                <div class="form-group">
                                    <label for="user_taget_groups"><?php _e('Target groups', 'municipio-intranet'); ?></label>
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

                        <div class="grid">
                            <div class="grid-md-6">
                                <div class="form-group">
                                    <label for="user_department"><?php _e('Department', 'municipio-intranet'); ?></label>
                                    <input type="text" id="user_department" name="user_department" value="{{ get_the_author_meta('user_department') }}">
                                </div>
                            </div>
                            <div class="grid-md-6">
                                <div class="form-group">
                                    <label for="user_phone"><?php _e('Phone number', 'municipio-intranet'); ?></label>
                                    <input type="tel" id="user_phone" name="user_phone" value="{{ get_the_author_meta('user_phone') }}">
                                </div>
                            </div>
                        </div>

                        <div class="grid">
                            <div class="grid-md-12">
                                <div class="form-group">
                                    <label for=""><?php _e('Profile image', 'municipio-intranet'); ?></label>
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

                        <div class="grid">
                            <div class="grid-xs-12">
                                <div class="form-group">
                                    <label for="user_about"><?php _e('About me', 'municipio-intranet'); ?></label>
                                    <textarea name="user_about" id="user_about" cols="30" rows="10">{{ get_the_author_meta('user_about') }}</textarea>
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
                    </form>
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

@stop
