<div class="box box-plain">
    <ul class="list-item-spacing">
        @foreach ($incidents as $incident)
        <li><a href="{{ get_blog_permalink($incident->blog_id, $incident->ID) }}" class="notice {{ $incident->incident_level }} pricon pricon-notice-{{ $incident->incident_level }} pricon-space-right">{{ $incident->post_title }}</a></li>
        @endforeach
    </ul>

    @if ($linkToArchive)
        <div class="">
            <a href="{{ get_post_type_archive_link('incidents') }}" class="pricon pricon-plus-o pricon-space-right text-sm"><?php _e('Show all incidents', 'municipio-intranet'); ?></a>
        </div>
    @endif
</div>

