@element([
    'classList' => [
        'municipio-trash-page__post-actions'
    ],
])
    <a style="color: black;" href="{!! wp_nonce_url(admin_url('post.php?post=' . $post->ID . '&action=untrash'), 'untrash-post_' . $post->ID) !!}">
        @element([
            'componentElement' => 'span',
            'classList' => [
                'material-symbols',
                'material-symbols-rounded',
                'material-symbols-sharp',
                'material-symbols-outlined',
                'municipio-trash-page__post-action-icon'
            ],
            'attributeList' => [
                'style' => 'margin-left: 0.25rem;'
            ] 
        ])
            undo
        @endelement
    </a>
    <a style="color: black;" href="{!! get_delete_post_link($post->ID, '', true) !!}" onclick="return confirm('{{ $lang['confirmDelete'] }}');">
        @element([
            'componentElement' => 'span',
            'classList' => [
                'material-symbols',
                'material-symbols-rounded',
                'material-symbols-sharp',
                'material-symbols-outlined',
                'municipio-trash-page__post-action-icon'
            ],
            'attributeList' => [
                'style' => 'margin-right: 0.25rem;'
            ] 
        ])
            delete
        @endelement
    </a>
@endelement