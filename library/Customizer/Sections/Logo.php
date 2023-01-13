<?php

namespace Municipio\Customizer\Sections;
use Kirki\Compatibility\Kirki;

class Logo
{
    public const SECTION_ID = "municipio_customizer_section_logo";

    public function __construct($panelID)
    {
        Kirki::add_section(self::SECTION_ID, array(
            'title'       => esc_html__('Logo', 'municipio'),
            'description' => esc_html__('Logo settings.', 'municipio'),
            'panel'          => $panelID,
            'priority'       => 160,
        ));

        $primaryLogoField = new \Kirki\Field\Image([
          'settings' => 'primary_logo',
          'label'       => esc_html__('Primary logo', 'municipio'),
          'section'     => self::SECTION_ID,
        ]);

        $secondaryLogoField = new \Kirki\Field\Image([
          'settings' => 'secondary_logo',
          'label'       => esc_html__('Secondary logo', 'municipio'),
          'section'     => self::SECTION_ID,
        ]);

        $emblemField = new \Kirki\Field\Image([
          'settings' => 'emblem',
          'label'       => esc_html__('Emblem', 'municipio'),
          'section'     => self::SECTION_ID,
        ]);

        Kirki::add_field($primaryLogoField);
        Kirki::add_field($secondaryLogoField);
        Kirki::add_field($emblemField);
    }
}
