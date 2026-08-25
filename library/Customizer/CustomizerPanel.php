<?php

namespace Municipio\Customizer;

use Throwable;
use WP_Customize_Manager;

class CustomizerPanel extends Panel
{
    public function handleRegistration(): bool
    {

        try {
            add_action('customize_register', function (WP_Customize_Manager $wpCustomize): void {
                $wpCustomize->add_panel($this->getID(), array_filter([
                    'title'           => $this->getTitle(),
                    'description'     => $this->getDescription(),
                    'priority'        => $this->getPriority(),
                    'active_callback' => $this->getActiveCallback(),
                    'theme_supports'  => $this->getThemeSupports(),
                    'type'            => $this->getType(),
                    'capability'      => $this->getCapability(),
                    'panel'           => $this->getPanel(),
                ], static fn($value): bool => $value !== '' && $value !== [] && $value !== null));
            });
        } catch (Throwable $e) {
            return false;
        }

        return true;
    }
}
