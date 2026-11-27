<?php

namespace Municipio\Customizer\Sections;

use Municipio\Controller\Sponsor\SponsorTemplateLabels;
use Municipio\Customizer\CustomizerPanel;
use Municipio\Customizer\CustomizerPanelSection;
use Municipio\Customizer\NativeField;
use Municipio\SchemaData\Config\SchemaDataConfigInterface;

class SingularTemplateLabels
{
    public static function register(SchemaDataConfigInterface $config): void
    {
        $panelId = 'municipio_single_labels';
        $sections = [];

        foreach ($config->getEnabledPostTypes() as $postType) {
            $type = get_post_type_object($postType);
            $schema = $config->tryGetSchemaTypeFromPostType($postType);

            if (!$type || !in_array($schema, ['SponsorDemand', 'SponsorOffer'], true)) {
                continue;
            }

            $sectionId = 'municipio_single_labels_' . $type->name;
            $sections[] = CustomizerPanelSection::create()
                ->setID($sectionId)
                ->setPanel($panelId)
                ->setTitle($type->labels->name);

            foreach (SponsorTemplateLabels::defaults($schema === 'SponsorDemand') as $key => $default) {
                NativeField::addField([
                    'type' => 'text',
                    'settings' => SponsorTemplateLabels::settingId($type->name, $key),
                    'label' => $default,
                    'section' => $sectionId,
                    'default' => '',
                    'input_attrs' => ['placeholder' => $default],
                ]);
            }
        }

        if ($sections) {
            CustomizerPanel::create()
                ->setID($panelId)
                ->setTitle(__('Detail views', 'municipio'))
                ->addSections($sections)
                ->register();
        }
    }
}
