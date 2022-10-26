@iframe([
	'src' => $src,
	'height' => $settings->height,
	'width' => $settings->width,
	'title' =>  $settings->title,
	'options' =>json_encode($settings->lang),
	'modifier' => 'video'
])
@endiframe