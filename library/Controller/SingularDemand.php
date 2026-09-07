<?php

namespace Municipio\Controller;

class SingularDemand extends \Municipio\Controller\Singular
{
    public string $view = 'single-schema-demand';

    public function init()
    {
        parent::init();
        $this->data['displayFeaturedImage'] = false;
        $this->populateLanguageObject();
        $this->populateInformationList();
    }

    private function populateLanguageObject(): void
    {
        $this->data['lang']->information      = __('Information', 'municipio');
        $this->data['lang']->validFrom        = __('Valid from', 'municipio');
        $this->data['lang']->validThrough     = __('Valid through', 'municipio');
        $this->data['lang']->availableFrom    = __('Available from', 'municipio');
        $this->data['lang']->availableThrough = __('Available through', 'municipio');
        $this->data['lang']->region           = __('Region', 'municipio');
        $this->data['lang']->seller           = __('Contact', 'municipio');
        $this->data['lang']->itemOffered      = __('Regarding', 'municipio');
        $this->data['lang']->moreInfo         = __('More information', 'municipio');
    }

    private function populateInformationList(): void
    {
        $this->data['informationList'] = [];

        if ($validFrom = $this->post->getSchemaProperty('validFrom')) {
            $this->data['informationList'][] = [
                'label' => $this->data['lang']->validFrom,
                'value' => $validFrom instanceof \DateTimeInterface ? $validFrom->format('Y-m-d') : $validFrom,
            ];
        }

        if ($validThrough = $this->post->getSchemaProperty('validThrough')) {
            $this->data['informationList'][] = [
                'label' => $this->data['lang']->validThrough,
                'value' => $validThrough instanceof \DateTimeInterface ? $validThrough->format('Y-m-d') : $validThrough,
            ];
        }

        if ($availabilityStarts = $this->post->getSchemaProperty('availabilityStarts')) {
            $this->data['informationList'][] = [
                'label' => $this->data['lang']->availableFrom,
                'value' => $availabilityStarts instanceof \DateTimeInterface ? $availabilityStarts->format('Y-m-d') : $availabilityStarts,
            ];
        }

        if ($availabilityEnds = $this->post->getSchemaProperty('availabilityEnds')) {
            $this->data['informationList'][] = [
                'label' => $this->data['lang']->availableThrough,
                'value' => $availabilityEnds instanceof \DateTimeInterface ? $availabilityEnds->format('Y-m-d') : $availabilityEnds,
            ];
        }

        if ($region = $this->post->getSchemaProperty('eligibleRegion')) {
            $this->data['informationList'][] = [
                'label' => $this->data['lang']->region,
                'value' => is_array($region) ? ($region['name'] ?? $region['@id'] ?? null) : $region,
            ];
        }

        if ($sellerName = ($this->post->getSchemaProperty('seller')['name'] ?? null)) {
            $this->data['informationList'][] = [
                'label' => $this->data['lang']->seller,
                'value' => $sellerName,
            ];
        }

        if ($itemOfferedName = ($this->post->getSchemaProperty('itemOffered')['name'] ?? null)) {
            $this->data['informationList'][] = [
                'label' => $this->data['lang']->itemOffered,
                'value' => $itemOfferedName,
            ];
        }
    }
}
