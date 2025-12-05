@iframe([
	'src'      => $src,
	'height'   => $settings->height,
	'width'    => $settings->width,
	'title'    => $settings->title,
	'labels'   => $settings->lang,
	'modifier' => 'video',
	'poster'   => $poster
])
@endiframe
