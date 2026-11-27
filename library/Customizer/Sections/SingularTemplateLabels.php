<?php

namespace Municipio\Customizer\Sections;

use Municipio\Controller\Sponsor\SponsorTemplateLabels;
use Municipio\Customizer\CustomizerPanelSection;
use Municipio\Customizer\NativeField;
use Municipio\SchemaData\Config\SchemaDataConfigInterface;

class SingularTemplateLabels
{
    public static function register(SchemaDataConfigInterface $config): void
    {
        foreach ($config->getEnabledPostTypes() as $postType) {
            $type = get_post_type_object($postType);
            $schema = $config->tryGetSchemaTypeFromPostType($postType);

            if (!$type || !in_array($schema, ['SponsorDemand', 'SponsorOffer'], true)) {
                continue;
            }

            $sectionId = 'municipio_single_labels_' . $type->name;
            CustomizerPanelSection::create()
                ->setID($sectionId)
                ->setTitle(sprintf(__('Detail view labels: %s', 'municipio'), $type->labels->singular_name))
                ->register();

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
    }
}
