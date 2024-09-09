<?php 

namespace Municipio\ImageConvert\Common;

class CreateContractReturn
{
    public static function createContractReturn(int $id, array $size): array
    {
      return [
        wp_get_attachment_url($id),
        $size[0],
        $size[1],
        false
      ];
    }
}