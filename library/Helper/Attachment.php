<?php

namespace Municipio\Helper;

class Attachment
{
    /**
     * Get attachment id from attachment url
     * @param  string $attachmentUrl Attachment url
     * @return integer               Attachment id
     */
    public static function getAttachmentId($attachmentUrl)
    {
        $attachmentId = 0;
        $dir = wp_upload_dir();

        if (false !== strpos($url, $dir['baseurl'] . '/')) { // Is URL in uploads directory?
            $file = basename($url);

            $queryArgs = array(
                'post_type'   => 'attachment',
                'post_status' => 'inherit',
                'fields'      => 'ids',
                'meta_query'  => array(
                    array(
                        'value'   => $file,
                        'compare' => 'LIKE',
                        'key'     => '_wp_attachment_metadata',
                    ),
                )
            );

            $query = new WP_Query($queryArgs);

            if ($query->have_posts()) {
                foreach ($query->posts as $postId) {
                    $meta = wp_get_attachment_metadata($postId);
                    $originalFile = basename($meta['file']);
                    $croppedImageFile = wp_list_pluck($meta['sizes'], 'file');

                    if ($originalFile === $file || in_array($file, $croppedImageFile)) {
                        $attachmentId = $postId;
                        break;
                    }
                }
            }
        }

        return $attachmentId;
    }
}
