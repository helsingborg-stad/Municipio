<?php

namespace Municipio\Controller;

use Municipio\Controller\Utils\SingularSchoolData;

/**
 * Class SingularSchool
 */
class SingularJobPosting extends \Municipio\Controller\Singular
{
    protected object $postMeta;

    public function init()
    {
        parent::init();
        $this->populateLanguageObject();
        $this->populateInformationList();
    }

    private function populateLanguageObject(): void
    {
        $this->data['lang']->contact                     = __('Contact', 'municipio');
        $this->data['lang']->administration              = __('Administration', 'municipio');
        $this->data['lang']->organizationAddressLocality = __('City', 'municipio');
        $this->data['lang']->datePosted                  = __('Date posted', 'municipio');
        $this->data['lang']->employmentType              = __('Employment type', 'municipio');
        $this->data['lang']->validThrough                = __('Valid through', 'municipio');
        $this->data['lang']->apply                       = __('Apply', 'municipio');
        $this->data['lang']->information                 = __('Information', 'municipio');
        $this->data['lang']->reference                   = __('Reference', 'municipio');
    }

    private function populateInformationList(): void
    {
        $this->data['informationList'] = [];

        if ($this->data['post']->schemaObject['validThrough'] ?? null) {
            $this->data['informationList'][] = [
                'label' => $this->data['lang']->validThrough,
                'value' => $this->data['post']->schemaObject['validThrough']
            ];
        }

        if ($this->data['post']->schemaObject['employmentType'] ?? null) {
            $this->data['informationList'][] = [
                'label' => $this->data['lang']->employmentType,
                'value' => $this->data['post']->schemaObject['employmentType']
            ];
        }

        if ($this->data['post']->schemaObject['datePosted'] ?? null) {
            $this->data['informationList'][] = [
                'label' => $this->data['lang']->datePosted,
                'value' => $this->data['post']->schemaObject['datePosted']
            ];
        }

        if ($this->data['post']->schemaObject['employmentUnit']['address']['addressRegion'] ?? null || $this->data['post']->schemaObject['employmentUnit']['address']['addressLocality'] ?? null) {
            $this->data['informationList'][] = [
                'label' => $this->data['lang']->organizationAddressLocality,
                'value' => join(', ', array_filter([$this->data['post']->schemaObject['employmentUnit']['address']['addressLocality'], $this->data['post']->schemaObject['employmentUnit']['address']['addressRegion']]))
            ];
        }

        if ($this->data['post']->schemaObject['employmentUnit']['name'] ?? null) {
            $this->data['informationList'][] = [
                'label' => $this->data['lang']->administration,
                'value' => $this->data['post']->schemaObject['employmentUnit']['name']
            ];
        }

        if ($this->data['post']->schemaObject['@id'] ?? null) {
            $this->data['informationList'][] = [
                'label' => $this->data['lang']->reference,
                'value' => $this->data['post']->schemaObject['@id']
            ];
        }
    }
}
