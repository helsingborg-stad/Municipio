<?php

namespace Municipio\Customizer;

use Kirki\Compatibility\Kirki;
use Throwable;

class KirkiPanel extends Panel
{
    public function handleRegistration(): bool
    {

        try {
            Kirki::add_panel($this->getID(), array(
                'title'           => $this->getTitle(),
                'description'     => $this->getDescription(),
                'priority'        => $this->getPriority(),
                'active_callback' => $this->getActiveCallback(),
                'theme_supports'  => $this->getThemeSupports(),
                'type'            => $this->getType(),
                'capability'      => $this->getCapability(),
                'panel'           => $this->getPanel()
            ));
        } catch (Throwable $e) {
            return false;
        }

        return true;
    }
}
