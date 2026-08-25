<?php

namespace Municipio\Customizer;

use Municipio\Customizer;
use WP_Customize_Manager;

class CustomizerPanelSection extends PanelSection
{
    public function handleRegistration(): PanelSection
    {

        add_action('customize_register', function (WP_Customize_Manager $wpCustomize): void {
            $wpCustomize->add_section($this->getID(), array_filter([
                'panel'              => $this->getPanel(),
                'title'              => $this->getTitle(),
                'description'        => $this->getDescription(),
                'priority'           => $this->getPriority(),
                'active_callback'    => $this->getActiveCallback(),
                'theme_supports'     => $this->getThemeSupports(),
                'type'               => $this->getType(),
                'capability'         => $this->getCapability(),
                'description_hidden' => $this->getDescriptionHidden(),
            ], static fn($value): bool => $value !== '' && $value !== [] && $value !== null));
        });

        if (filter_var($this->getPreviewUrl(), FILTER_VALIDATE_URL)) {
            Customizer::$panels[$this->getID()] = $this->getPreviewUrl();
        }

        return $this;
    }
}
