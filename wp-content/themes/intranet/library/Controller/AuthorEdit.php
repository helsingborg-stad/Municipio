<?php

namespace Intranet\Controller;

class AuthorEdit extends \Municipio\Controller\BaseController
{
    public function init()
    {
        global $wp_query;
        global $authordata;

        // Save form if posted
        $this->saveForm();

        // Get other data
        $currentUser = wp_get_current_user();
        $user = get_user_by('slug', $wp_query->query['author_name']);

        if ($user) {
            $authordata = $user;
        }

        $this->data['currentUser'] = $currentUser;
        $this->data['user'] = $user;
        $this->data['administrationUnits'] = apply_filters('MunicipioIntranet/administration_units', array(
            'Arbetsmarknadsförvaltningen',
            'Fastighetsförvaltningen',
            'Kulturförvaltningen',
            'Miljöförvaltningen',
            'Skol- och fritidsförvaltningen',
            'Socialförvaltningen',
            'Stadsbyggnadsförvaltningen',
            'Stadsledningsförvaltningen',
            'Vård- och omsorgsförvaltningen'
        ));
    }

    private function saveForm()
    {
        global $wp_query;

        if (!isset($_POST['_wpnonce'])) {
            return;
        }

        $currentUser = wp_get_current_user();
        $user = get_user_by('slug', $wp_query->query['author_name']);

        if (!wp_verify_nonce($_REQUEST['_wpnonce'], 'user_settings_update_' . $user->ID)) {
            return;
        }

        if (isset($_POST['image_uploader_file'][0]) && !empty($_POST['image_uploader_file'][0])) {
            $this->uploadProfileImage($_POST['image_uploader_file'][0], $user);
        }

        update_user_meta($user->ID, 'user_work_title', sanitize_text_field($_POST['user_work_title']));
        update_user_meta($user->ID, 'user_phone', sanitize_text_field($_POST['user_phone']));
        update_user_meta($user->ID, 'user_administration_unit', sanitize_text_field($_POST['user_administration_unit']));
        update_user_meta($user->ID, 'user_department', sanitize_text_field($_POST['user_department']));

        return true;
    }

    public function uploadProfileImage($imageDataUri, $user)
    {
        global $current_site;

        // Decode the imageDataUri
        $imageDataUri = str_replace(' ', '+', $imageDataUri);
        $decodedImage = base64_decode($imageDataUri);

        switch_to_blog($current_site->blog_id);

        $uploadDir = wp_upload_dir();
        $uploadDirUrl = $uploadDir['baseurl'];
        $uploadDir = $uploadDir['basedir'];
        $uploadDir = $uploadDir . '/profile-images';

        var_dump($uploadDir);

        restore_current_blog();

        //file_put_contents(wp_upload_dir(), data)
        //var_dump($user);
        exit;
    }
}
