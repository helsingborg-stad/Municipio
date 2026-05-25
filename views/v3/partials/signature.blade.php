<!-- Signature -->
{!! $hook->signatureBefore !!}
@signature([
    'author' => $name,
    'published' => isset($published) ? $published : null,
    'updated' => $updated,
    'avatar_size' => 'sm',
    'avatar' => $avatar,
    'authorRole' => $role,
    'link' => $link,
    'updatedLabel' => $lang->updated,
    'publishedLabel' => $lang->publish,
    'classList' => $classList ?? []
])
@endsignature
{!! $hook->signatureAfter !!}
