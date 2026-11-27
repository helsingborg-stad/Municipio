<?php

namespace Municipio\Controller\Sponsor;

class SponsorTemplateLabels
{
    public static function defaults(bool $isDemand): array
    {
        $labels = [
            'type' => $isDemand ? __('Sponsorship request', 'municipio') : __('Sponsorship offer', 'municipio'),
            'contact_link' => __('Go to contact details', 'municipio'),
            'description' => $isDemand ? __('About the activity', 'municipio') : __('About the offer', 'municipio'),
            'summary' => __('Sponsorship at a glance', 'municipio'),
            'support_date' => $isDemand ? __('Support needed by', 'municipio') : __('Sponsorship date', 'municipio'),
            'location' => __('Sponsorship location', 'municipio'),
            'resources' => $isDemand ? __('What the association needs', 'municipio') : __('What the company wants to contribute', 'municipio'),
            'practical_information' => __('Things to keep in mind', 'municipio'),
            'counter_performance' => $isDemand ? __('What the association can offer in return', 'municipio') : __('What the company would like in return', 'municipio'),
            'contacts' => __('Contact details', 'municipio'),
            'organization' => $isDemand ? __('About the association', 'municipio') : __('About the company', 'municipio'),
            'organization_link' => $isDemand ? __('Visit the association’s website', 'municipio') : __('Visit the company’s website', 'municipio'),
            'preferences' => __('What the association looks for in a sponsor', 'municipio'),
        ];

        if ($isDemand) {
            $labels['activities'] = __('Main activity of the association', 'municipio');
        }

        return $labels;
    }

    public static function settingId(string $postType, string $key): string
    {
        return "single_{$postType}_label_{$key}";
    }

    public static function forPost(int $postId, bool $isDemand): array
    {
        $postType = get_post_type($postId);
        $labels = self::defaults($isDemand);
        $editUrls = [];
        // Municipio redirects anyone with the editor role away from customize.php.
        $roles = wp_get_current_user()->roles;
        $previewUrl = !is_customize_preview() && in_array('administrator', $roles, true)
            && !in_array('editor', $roles, true) && current_user_can('customize')
            ? get_permalink($postId)
            : false;

        foreach ($labels as $key => $default) {
            $settingId = self::settingId((string) $postType, $key);
            $saved = get_theme_mod($settingId, '');
            $labels[$key] = is_string($saved) && trim($saved) !== '' ? $saved : $default;
            if ($previewUrl) {
                $editUrls[$key] = add_query_arg([
                    'url' => urlencode($previewUrl),
                    'autofocus[control]' => $settingId,
                ], admin_url('customize.php'));
            }
        }

        return ['labels' => $labels, 'editUrls' => $editUrls];
    }
}
