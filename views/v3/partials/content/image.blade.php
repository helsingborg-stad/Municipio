@image([
    'src'=> !empty($src) ? $src : false,
    'alt' => !empty($alt) ? $alt : false,
    'imgAttributeList' => !empty($imgAttributeList) && is_array($imgAttributeList) ? $imgAttributeList : [],
    'attributeList' => !empty($attributeList) && is_array($attributeList) ? $attributeList : [],
    'caption'   => !empty($caption) ? $caption : false,
    'classList' => !empty($classList) ? $classList : [],
    'lqipEnabled' => false
])
@endimage