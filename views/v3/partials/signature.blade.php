<!-- Signature -->
{!! $hook->signatureBefore !!}
@signature([
    'author' => $name ?? '',
    'published' => $published ?? false,
    'updated' => $updated ?? false,
    'avatar_size' => 'sm',
    'avatar' => $avatar ?? '',
    'authorRole' => $role ?? '',
    'link' => $link ?? '',
    'updatedLabel' => $lang->updated,
    'publishedLabel' => $lang->publish,
    'classList' => $classList ?? []
])
@endsignature
{!! $hook->signatureAfter !!}
