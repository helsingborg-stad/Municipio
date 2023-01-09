<!-- Signature -->
@if (!empty($signature) && !empty($postTypeDetails))
    @if ($postTypeDetails->hierarchical && !empty($publishTranslations))
        @signature([
            'author' => $signature->name,
            'published' => $signature->published,
            'updated' => $signature->updated,
            'avatar_size' => 'sm',
            'avatar' => $signature->avatar,
            'authorRole' => $signature->role,
            'link' => $signature->link,
            'updatedLabel' => $publishTranslations->updated,
            'publishedLabel' => $publishTranslations->publish,
            'classList' => $classList ?? []
        ])
        @endsignature
    @elseif(!$postTypeDetails->hierarchical && $postType == 'post')
        @signature([
            'published' => $signature->published,
            'updated' => $signature->updated,
            'updatedLabel' => $publishTranslations->updated,
            'publishedLabel' => $publishTranslations->publish
        ])
        @endsignature
    @endif
@endif
