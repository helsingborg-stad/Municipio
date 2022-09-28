@iframe([
	'src' => $src,
	'height' => $data->height,
	'width' => $data->width,
	'title' =>  $data->title,
    'classList' => ['js-suppressed-iframe'],
])
@endiframe
