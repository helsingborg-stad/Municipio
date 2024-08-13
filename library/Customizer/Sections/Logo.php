<?php

namespace Municipio\Customizer\Sections;

use Municipio\Customizer\KirkiField;

class Logo
{
    public function __construct(string $sectionID)
    {
        $primaryDescription   = esc_html__('Only accepts .svg-files (Scalable Vector Graphics).', 'municipio');
        $secondaryDescription = esc_html__(
            'Upload your secondary logotype in .svg format (Scalable Vector Graphics).
            The secondary logotype is usually 100% white and can be used on dark or colored backgrounds.',
            'municipio'
        );
        $emblemDescription    = esc_html__(
            'Upload an emblem in .svg format (Scalable Vector Graphics).
            The emblem will be used to strengthen the website brand, when a sub brand is used.',
            'municipio'
        );

        $primaryLogoField = $this->getImageField(
            $sectionID,
            'logotype',
            esc_html__('Primary logo', 'municipio'),
            $primaryDescription
        );

        $secondaryLogoField = $this->getImageField(
            $sectionID,
            'logotype_negative',
            esc_html__('Secondary logo', 'municipio'),
            $secondaryDescription
        );

        $emblemField = $this->getImageField(
            $sectionID,
            'logotype_emblem',
            esc_html__('Emblem', 'municipio'),
            $emblemDescription
        );

        KirkiField::addField($primaryLogoField);
        KirkiField::addField($secondaryLogoField);
        KirkiField::addField($emblemField);
    }

    private function getImageField(string $sectionID, string $setting, string $label, string $description): array
    {
        return [
            'type'        => 'upload',
            'mime_type'   => 'image/svg+xml',
            'settings'    => $setting,
            'label'       => $label,
            'description' => $description,
            'section'     => $sectionID,
            'output'      => [
                [
                    'type'      => 'controller',
                    'as_object' => false,
                ]
            ]
        ];
    }
}
