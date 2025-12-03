<?php

namespace Municipio\Customizer;

use Kirki\Compatibility\Kirki;

class KirkiPanelSection extends PanelSection
{
    public function handleRegistration(): PanelSection
    {

        Kirki::add_section($this->getID(), array(
            'panel'              => $this->getPanel(),
            'title'              => $this->getTitle(),
            'description'        => $this->getDescription(),
            'priority'           => $this->getPriority(),
            'active_callback'    => $this->getActiveCallback(),
            'theme_supports'     => $this->getThemeSupports(),
            'type'               => $this->getType(),
            'capability'         => $this->getCapability(),
            'description_hidden' => $this->getDescriptionHidden(),
            'preview_url'        => $this->getPreviewUrl(),
            'tabs'               => $this->getTabs()
        ));

        return $this;
    }
}
