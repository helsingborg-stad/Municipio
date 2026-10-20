<?php

namespace Municipio\Controller;

class SingularSponsorOffer extends Singular
{
    public string $view = 'single-schema-sponsor-offer';

    public function init()
    {
        parent::init();
        if (isset($this->post)) {
            $presentation = new Sponsor\SponsorPresentation($this->wpService);
            $this->data['sponsor'] = $presentation->map($this->post->getSchema());
            $this->data['sponsorTypeLabel'] = $this->wpService->__('Offering sponsorship', 'municipio');
            $this->data['sponsorDescriptionHeading'] = $this->wpService->__('About the offer', 'municipio');
            $this->data['sponsorRequirementsHeading'] = $this->wpService->__('Sponsorship conditions', 'municipio');
            $this->data['sponsorOrganizationHeading'] = $this->wpService->__('About the company', 'municipio');
            $this->data['sponsorOrganizationLabel'] = $this->wpService->__('Company', 'municipio');
        }
        return $this->data;
    }
}
