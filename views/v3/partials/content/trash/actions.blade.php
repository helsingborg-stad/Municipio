@element([
    'classList' => [
        'municipio-trash-page__post-actions'
    ],
])
    <a style="color: black;" href="{!! wp_nonce_url(admin_url('post.php?post=' . $post->ID . '&action=untrash'), 'untrash-post_' . $post->ID) !!}">
        @icon(['icon' => 'undo', 'classList' => ['municipio-trash-page__post-action-icon']])
    </a>
    <a style="color: black;" href="{!! get_delete_post_link($post->ID, '', true) !!}" onclick="return confirm('{{ $lang['confirmDelete'] }}');">
        @icon(['icon' => 'delete', 'classList' => ['municipio-trash-page__post-action-icon']])
    </a>
@endelement
