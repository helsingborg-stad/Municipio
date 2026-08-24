<?php

namespace Municipio\Customizer\Sections;

use Municipio\Customizer\CustomizerField;

class Logo
{
    public function __construct(string $sectionID)
    {
        $siteIdentitySectionID = 'title_tagline';

        $primaryDescription = esc_html__('Svg format is preferred.', 'municipio');
        $secondaryDescription = esc_html__(
            'Svg format is preferred. This logo may be a maskable format (plain single color logo).',
            'municipio',
        );
        $emblemDescription = esc_html__(
            'Upload an emblem in .svg format (Scalable Vector Graphics).
            The emblem will be used to communicate main publisher, when a sub brand is used.',
            'municipio',
        );

        $primaryLogoField = $this->getImageField(
            $siteIdentitySectionID,
            'logotype',
            esc_html__('Primary logo', 'municipio'),
            $primaryDescription,
        );

        $secondaryLogoField = $this->getImageField(
            $siteIdentitySectionID,
            'logotype_negative',
            esc_html__('Secondary logo', 'municipio'),
            $secondaryDescription,
        );

        $emblemField = $this->getImageField(
            $siteIdentitySectionID,
            'logotype_emblem',
            esc_html__('Emblem', 'municipio'),
            $emblemDescription,
        );

        CustomizerField::addField($primaryLogoField);
        CustomizerField::addField($secondaryLogoField);
        CustomizerField::addField($emblemField);
    }

    private function getImageField(string $sectionID, string $setting, string $label, string $description): array
    {
        return [
            'type' => 'upload',
            'mime_type' => 'image/svg+xml',
            'settings' => $setting,
            'label' => $label,
            'description' => $description,
            'section' => $sectionID,
            'output' => [
                [
                    'type' => 'controller',
                    'as_object' => false,
                ],
            ],
        ];
    }
}
