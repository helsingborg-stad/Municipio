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
        }
        return $this->data;
    }
}
