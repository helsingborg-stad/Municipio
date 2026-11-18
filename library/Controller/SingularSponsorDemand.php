<?php

namespace Municipio\Controller;

class SingularSponsorDemand extends Singular
{
    public string $view = 'single-schema-sponsor-demand';

    public function init()
    {
        parent::init();
        if (isset($this->post)) {
            $presentation = new Sponsor\SponsorPresentation($this->wpService);
            $this->data['sponsor'] = $presentation->map($this->post->getSchema());
            $this->data['sponsorTypeLabel'] = $this->wpService->__('Seeking sponsorship', 'municipio');
            $this->data['sponsorDescriptionHeading'] = $this->wpService->__('About the sponsorship request', 'municipio');
            $this->data['sponsorOrganizationHeading'] = $this->wpService->__('About the association', 'municipio');
        }
        return $this->data;
    }
}
