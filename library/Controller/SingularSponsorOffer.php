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
        }
        return $this->data;
    }
}
