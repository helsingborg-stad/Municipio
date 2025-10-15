<?php

namespace Modularity\Module\Contacts;

use Modularity\Integrations\Component\ImageResolver;
use ComponentLibrary\Integrations\Image\Image as ImageComponentContract;
class Contacts extends \Modularity\Module
{
    public $slug = 'contacts';
    public $supports = array();
    public $displaySettings = null;
    
    private $view = null;
    public function init()
    {
        $this->nameSingular = __('Contacts v2', 'modularity');
        $this->namePlural = __('Contacts v2', 'modularity');
        $this->description = __('Outputs one or more contacts', 'modularity');
    }

    public function data() : array
    {
        $data = $this->getFields();
        $data['ID'] = $this->ID;

        $this->view = $data['view'] ?? 'extended';
        $data['view'] = $this->view;

        if(!empty($data['contacts'])) {
            $data['contacts'] = $this->prepareContacts($data['contacts']);
        }
        
        if (!isset($data['columns'])) {
            $data['columns'] = 'o-grid-12@md';
        }

        if ($data['view'] === 'simple') {
            $data['columns'] .= ' o-grid-6@sm';
        }

        return $data;
    }

    /**
     * Prepare the contact data
     * @param  array $contacts
     * @return array
     */
    public function prepareContacts($contacts)
    {
        $retContacts = array();

        foreach ($contacts as &$contact) {
            $info = array(
                'image' => null,
                'first_name' => null,
                'last_name' => null,
                'work_title' => null,
                'administration_unit' => null,
                'email' => null,
                'phone' => null,
                'social_media' => null,
                'address' => null,
                'visiting_address' => null,
                'opening_hours' => null
            );

            switch ($contact['acf_fc_layout']) {
                case 'custom':
                    $info = apply_filters('Modularity/mod-contacts/contact-info', array(
                        'image'               => $contact['image'],
                        'first_name'          => $contact['first_name'],
                        'last_name'           => $contact['last_name'],
                        'work_title'          => $contact['work_title'],
                        'administration_unit' => $contact['administration_unit'],
                        'email'               => strtolower($contact['email']),
                        'phone'               => $contact['phone_numbers'] ?? null,
                        'social_media'        => $contact['social_media'] ?? null,
                        'address'             => strip_tags($contact['address'] ?? '', '<br>'),
                        'visiting_address'    => strip_tags($contact['visiting_address'] ?? '', ['<br>', '<a>']),
                        'opening_hours'       => strip_tags($contact['opening_hours'], '<br>'),
                        'other'               => $contact['other']
                    ), $contact, $contact['acf_fc_layout']);
                    break;

                case 'user':
                    $fields = function_exists('get_fields') ? get_fields('user_' . $contact['user']['ID']) : [];
                    $info = apply_filters('Modularity/mod-contacts/contact-info', array(
                         'id'                  => !empty($contact['user']['ID']) ? $contact['user']['ID'] : '',
                         'image'               => $fields['user_profile_picture_id'] ?? null,
                         'first_name'          => $contact['user']['user_firstname'] ?? '',
                         'last_name'           => $contact['user']['user_lastname'] ?? '',
                         'work_title'          => null,
                         'administration_unit' => null,
                         'email'               => $contact['user']['user_email'] ?? '',
                         'phone'               => $fields['phone_numbers'] ?: null,
                         'social_media'        => $this->getUserSocialMedia($fields ?: []),
                         'address'             => strip_tags($fields['address'] ?? '', '<br>'),
                         'visiting_address'    => strip_tags($fields['visiting_address'] ?? '', ['<br>', '<a>']),
                         'opening_hours'       => null,
                         'other'               => $contact['user']['user_description'] ?? '',
                     ), $contact, $contact['acf_fc_layout']);
                    break;
            }

            $attachmentId = null;

            if (isset($info['image']) && !empty($info['image']) && isset($info['image']['id']) && is_numeric($info['image']['id'])) {
                $attachmentId = $info['image']['id'];
            } elseif (!empty($info['image']) && filter_var($info['image'], FILTER_VALIDATE_URL)) {
                $attachmentId = attachment_url_to_postid($info['image']);
            }

            if (!empty($attachmentId) && $this->view === 'extended') {
                $info['thumbnail'] = wp_get_attachment_image_src(
                    $attachmentId,
                    [400, 400]
                )[0];
            } elseif (!empty($attachmentId) && $this->view === 'simple') {
                $info['thumbnail'] = ImageComponentContract::factory(
                    (int) $attachmentId,
                    [1024, 1024],
                    new ImageResolver()
                );
            }

            //Create full name
            $info['full_name'] = trim($info['first_name'] . ' ' . $info['last_name']);

            //Create full title string
            $titleProperties = ['administration_unit', 'work_title'];
            $fullTitle = array_filter(array_map(function($key) use ($info) {
                return $info[$key] ?: false;
            }, $titleProperties), function($item) {return $item;});
            $info['full_title'] = is_array($fullTitle) ? implode(', ', $fullTitle) : '';

            //Sanitize social media
            if (!empty($info['social_media'])) {
                $info['social_media'] = array_filter($info['social_media'], function($item) {
                    return !empty($item['url']);
                });
            }

            //Add social media labels by ['media'] key
            if (!empty($info['social_media'])) {
                $info['social_media'] = array_map(function($item) {

                    switch ($item['media']) {
                        case 'linkedin':
                            $item['label'] = 'LinkedIn';
                            break;
                        case 'twitter':
                            $item['label'] = 'X';
                            break;
                        case 'instagram':
                            $item['label'] = 'Instagram';
                            break;
                        case 'facebook':
                            $item['label'] = 'Facebook';
                            break;
                    }

                    return $item;
                }, $info['social_media']);
            }

            // Restructure legacy opening hours for new component format
            if (isset($info['opening_hours']) && !empty($info['opening_hours'])) {
                $info['custom_sections'] = [
                    [
                        'title' => __('Opening hours', 'modularity'),
                        'content' => $info['opening_hours']
                    ]
                ];
            }

            //Contact returns
            $retContacts[] = $info;
        }

        return $retContacts;
    }

    private function getUserSocialMedia(array $fields): ?array {
        
        $socialMedia = array();

        if(!empty($fields['user_facebook_url'])) {
            $socialMedia[] = ['media' => 'facebook', 'url' => $fields['user_facebook_url']];
        }

        if(!empty($fields['user_twitter_username'])) {
            $socialMedia[] = ['media' => 'twitter', 'url' => $fields['user_twitter_username']];
        }

        if(!empty($fields['user_linkedin_url'])) {
            $socialMedia[] = ['media' => 'linkedin', 'url' => $fields['user_linkedin_url']];
        }

        if(!empty($fields['user_instagram_username'])) {
            $socialMedia[] = ['media' => 'instagram', 'url' => $fields['user_instagram_username']];
        }

        return $socialMedia ?: null;
    }

    public function template()
    {
        return 'cards.blade.php';
    }

    /**
     * Available "magic" methods for modules:
     * init()            What to do on initialization
     * data()            Use to send data to view (return array)
     * style()           Enqueue style only when module is used on page
     * script            Enqueue script only when module is used on page
     * adminEnqueue()    Enqueue scripts for the module edit/add page in admin
     * template()        Return the view template (blade) the module should use when displayed
     */
}
