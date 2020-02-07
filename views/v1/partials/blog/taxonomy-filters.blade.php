@foreach ($enabledSidebarFilters as $taxonomy)
    <?php $taxs = get_terms($taxonomy); $taxonomyVars = get_taxonomy($taxonomy); ?>

    @if (count($taxs) > 0)
        <div class="grid-xs-12">
            <div class="box box-filled">
                <h4 class="box-title">{{ $taxonomyVars->labels->name }}</h4>
                <div class="box-content">
                    <ul>
                        <li><a href=""><?php _e('Show all', 'municipio'); ?></a></li>
                    @foreach ($taxs as $tax)
                        <li><a class="{{ isset($_GET['tax']) && $_GET['tax'] == $taxonomyVars->name && isset($_GET['term']) && $_GET['term'] == $tax->slug ? 'active' : '' }}" href="?{{ \Municipio\Helper\Url::queryStringExclude($_SERVER['QUERY_STRING'], array('tax', 'term'), '&amp;') }}tax={{ $taxonomyVars->name }}&amp;term={{ $tax->slug }}">{{ $tax->name }}</a></li>
                    @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif
@endforeach
