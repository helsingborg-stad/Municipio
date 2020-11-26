<!-- Signature -->
@if($postTypeDetails->hierarchical)
  @signature([
      'author' => $signature->name, 
      'published' => $signature->publish,
      'updated' => $signature->updated,
      'avatar_size' => 'sm',
      'avatar' => $signature->avatar,
      'authorRole' => $signature->role,
      'link' => $signature->link,
      'updatedLabel' => $publishTranslations->updated,
      'publishedLabel' => $publishTranslations->publish,
      'classList' => $classList
  ])
  @endsignature
@elseif(!$postTypeDetails->hierarchical && $postType == 'post')
  @signature([
      'published' => $signature->publish,
      'updated' => $signature->updated,
      'updatedLabel' => $publishTranslations->updated,
      'publishedLabel' => $publishTranslations->publish,
      'classList' => ['u-margin__y--2']
  ])
  @endsignature
@endif