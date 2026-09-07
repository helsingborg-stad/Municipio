<?php

namespace Municipio\Controller;

class SingularOffer extends \Municipio\Controller\Singular
{
    public string $view = 'single-schema-offer';

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
        $this->data['lang']->price            = __('Price', 'municipio');
        $this->data['lang']->validFrom        = __('Valid from', 'municipio');
        $this->data['lang']->validThrough     = __('Valid through', 'municipio');
        $this->data['lang']->availableFrom    = __('Available from', 'municipio');
        $this->data['lang']->availableThrough = __('Available through', 'municipio');
        $this->data['lang']->region           = __('Region', 'municipio');
        $this->data['lang']->offeredBy        = __('Offered by', 'municipio');
        $this->data['lang']->itemOffered      = __('Item', 'municipio');
        $this->data['lang']->moreInfo         = __('More information', 'municipio');
    }

    private function populateInformationList(): void
    {
        $this->data['informationList'] = [];

        $price    = $this->post->getSchemaProperty('price');
        $currency = $this->post->getSchemaProperty('priceCurrency');
        if ($price !== null) {
            $this->data['informationList'][] = [
                'label' => $this->data['lang']->price,
                'value' => trim($price . ($currency ? ' ' . $currency : '')),
            ];
        }

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

        // offeredBy takes precedence over seller
        $offerer = $this->post->getSchemaProperty('offeredBy') ?? $this->post->getSchemaProperty('seller');
        if ($offererName = (is_array($offerer) ? ($offerer['name'] ?? null) : $offerer)) {
            $this->data['informationList'][] = [
                'label' => $this->data['lang']->offeredBy,
                'value' => $offererName,
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
