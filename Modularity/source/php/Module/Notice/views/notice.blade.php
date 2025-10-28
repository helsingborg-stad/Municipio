@notice([
    'stretch' => (!is_admin() && isset($blockData) ? ((bool) $blockData['align'] == 'full') : $stretch ?? false),
    'type' => $notice_type,
    'message' => [
        'title' => !$hideTitle && !empty($postTitle) ? $postTitle : null,
        'text' => $notice_text,
    ],
    'action' => isset($include_link) && $include_link === true && is_array($link) 
        ? [
            'text' => isset($link['title']) ? $link['title'] : $link['url'],
            'url' => $link['url'],
            'position' => $link_position,
         ] 
        : null,
    'dismissable' => isset($dismissible) && $dismissible ? $dismissal_time : false,
    'icon' => $icon,
    'context' => ['notice', 'module.notice']
])
@endnotice