<!-- Signature -->
@section('signature.before')@show
@signature([
    'author' => $name,
    'published' => $published,
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
@section('signature.after')@show