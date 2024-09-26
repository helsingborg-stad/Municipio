@image([
    'src'=> !empty($src) ? $src : false,
    'alt' => !empty($alt) ? $alt : false,
    'imgAttributeList' => !empty($imgAttributeList) && is_array($imgAttributeList) ? $imgAttributeList : [],
    'caption'   => !empty($caption) ? $caption : false,
    'classList' => !empty($classList) ? $classList : [],
])
@endimage
