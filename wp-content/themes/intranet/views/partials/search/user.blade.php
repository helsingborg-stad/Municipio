<?php $faces = array('pricon-smiley-flirty', 'pricon-smiley-cool', 'pricon-smiley-smile', 'pricon-smiley-super-happy', 'pricon-smiley-thounge'); ?>
<ul class="grid" data-equal-container>
    @foreach ($users as $item)
    <?php global $authordata; $authordata = get_user_by('ID', $item->ID); ?>
    <li class="grid-sm-12 grid-md-6 grid-lg-4">
        <a href="#" class="profile-card profile-header" data-equal-item>
            @if (!empty(get_the_author_meta('user_profile_picture')))
            <div class="profile-image" style="background-image:url('{{ get_the_author_meta('user_profile_picture') }}');"></div>
            @else
            <div class="profile-image">
                <i class="pricon {{ $faces[array_rand($faces)] }}"></i>
            </div>
            @endif

            <span class="h4">{{ municipio_intranet_get_user_full_name(get_the_author_meta('ID')) }}</span>

            @if (!empty(get_the_author_meta('ad_title')))
                 <span class="profile-title">{{ get_the_author_meta('ad_title') }}</span>
            @elseif (!empty(get_the_author_meta('user_work_title')))
                <span class="profile-title">{{ get_the_author_meta('user_work_title') }}</span>
            @endif

            @if (!empty(get_the_author_meta('user_administration_unit')) || !empty(get_the_author_meta('user_department')))
                <span class="profile-department">
                @if (!empty(get_the_author_meta('user_administration_unit')))
                    @foreach ((array) get_the_author_meta('user_administration_unit') as $unit)
                        {{ municipio_intranet_get_administration_unit_name($unit) }}
                    @endforeach
                @endif
                </span>
            @endif
        </a>
    </li>
    @endforeach
</ul>
