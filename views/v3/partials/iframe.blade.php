@iframe([
	'src' => $src,
	'height' => $settings->height,
	'width' => $settings->width,
	'title' =>  $settings->title,
   'classList' => ['js-suppressed-iframe'],
	'options' =>json_encode($settings->lang),
	'embedVideo' => 'js-suppressed-video'
])
@endiframe