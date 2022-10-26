@iframe([
	'src' => $src,
	'height' => $data->height,
	'width' => $data->width,
	'title' =>  $data->title,
	'options' =>json_encode($data->lang),
	'modifier' => '--video'
])
@endiframe