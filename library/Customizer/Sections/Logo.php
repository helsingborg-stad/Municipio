<?php

namespace Municipio\Customizer\Sections;
use Kirki\Compatibility\Kirki;
use Kirki\Field\Image as ImageField;

class Logo
{
    public const SECTION_ID = "municipio_customizer_section_logo";

    public function __construct($panelID)
    {

        $primaryLogoField = $this->getImageField(self::SECTION_ID, 'logotype', esc_html__('Primary logo', 'municipio'), esc_html__('Only accepts .svg-files (Scalable Vector Graphics).', 'municipio'));
        $secondaryLogoField = $this->getImageField(self::SECTION_ID, 'logotype_negative', esc_html__('Secondary logo', 'municipio'), esc_html__('Upload your secondary logotype in .svg format (Scalable Vector Graphics). The secondary logotype is usually 100% white and can be used on dark or colored backgrounds.', 'municipio'));
        $emblemField = $this->getImageField(self::SECTION_ID, 'logotype_emblem', esc_html__('Emblem', 'municipio'), esc_html__('Upload an emblem in .svg format (Scalable Vector Graphics). The emblem will be used to strengthen the website brand, when a sub brand is used.', 'municipio'), 'url');
      
        $this->addSection($panelID);
        Kirki::add_field($primaryLogoField);
        Kirki::add_field($secondaryLogoField);
        Kirki::add_field($emblemField);
    }

    private function addSection(string $panelID) {
      Kirki::add_section(self::SECTION_ID, array(
        'title'         => esc_html__('Logo', 'municipio'),
        'description'   => esc_html__('Logo settings.', 'municipio'),
        'panel'         => $panelID,
        'priority'      => 160,
      ));
    }

    private function getImageField(string $sectionID, string $setting, string $label, string $description, string $saveAs = 'array'):ImageField {
      $sanitizedSaveAs = $saveAs === 'url' ?? 'array';
      return new ImageField([
          'settings'      => $setting,
          'label'         => $label,
          'description'   => $description,
          'section'       => $sectionID,
          'choices'       => [
            'save_as' => $sanitizedSaveAs,
          ],
      ]);
    }
}
