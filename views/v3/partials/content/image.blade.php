@image([
    'src'=> !empty($src) ? $src : false,
    'alt' => !empty($alt) ? $alt : false,
    'heading' => !empty($heading) ? $heading : false,
    'imgAttributeList' => !empty($imgAttributeList) ? $imgAttributeList : [],
    'openModal' => !empty($openModal) ? $openModal : false,
    'isPanel' => !empty($isPanel) ? $isPanel : false,
    'caption'   => !empty($caption) ? $caption : false,
    'classList' => !empty($classList) ? $classList : [],
])
@endimage
