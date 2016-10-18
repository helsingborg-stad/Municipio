<ul class="grid" data-equal-container>
    @foreach ($results as $item)
    <?php global $authordata; $authordata = get_user_by('ID', $item->ID); ?>
    <li class="grid-sm-12 grid-md-6 grid-lg-4">
        <a href="#" class="profile-card profile-header" data-equal-item>
            @if (!empty(get_the_author_meta('user_profile_picture')))
            <div class="profile-image" style="background-image:url('{{ get_the_author_meta('user_profile_picture') }}');"></div>
            @else
            <div class="profile-image"></div>
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



<!--
<li>
    <div class="search-result-item">
        <div class="search-result-item-user">
            <div class="profile-header-background" style="background-image:url('{{ !empty(get_the_author_meta('user_profile_picture')) ? get_the_author_meta('user_profile_picture') : 'http://www.helsingborg.se/wp-content/uploads/2016/05/varen_2016_2_1800x350.jpg' }}');"></div>

            @if (!empty(get_the_author_meta('user_profile_picture')))
            <div class="profile-image" style="background-image:url('{{ get_the_author_meta('user_profile_picture') }}');"></div>
            @endif

            <div class="profile-basics">
                <h3><a href="{{ municipio_intranet_get_user_profile_url($item->ID) }}">{{ municipio_intranet_get_user_full_name(get_the_author_meta('ID')) }}</a></h3>




            </div>
        </div>

        <div class="search-result-info">
            <span class="search-result-url"><i class="fa fa-user"></i> <a href="{{ municipio_intranet_get_user_profile_url($item->ID) }}">{{ municipio_intranet_get_user_profile_url($item->ID) }}</a></span>
        </div>
    </div>
</li>
-->
