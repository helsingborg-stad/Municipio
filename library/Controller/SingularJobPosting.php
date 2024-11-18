<?php

namespace Municipio\Controller;

/**
 * Class SingularJobPosting
 */
class SingularJobPosting extends \Municipio\Controller\Singular
{
    protected object $postMeta;
    public string $view = 'single-schema-jobposting';

    /**
     * @inheritDoc
     */
    public function init()
    {

        parent::init();


        $this->data['post'] = $this->sanitizeValidThroughDateString($this->data['post']);
        $this->populateLanguageObject();
        $this->populateInformationList();
        $this->setExpired();
    }

    /**
     * Sanitize the validThrough date string.
     *
     * @param object $post
     * @return object
     */
    private function sanitizeValidThroughDateString($post): object
    {
        if (empty($post->schemaObject['validThrough'])) {
            return $post;
        }

        try {
            $date = new \DateTime($post->schemaObject['validThrough']);
            $post->schemaObject->setProperty('validThrough', $date->format('Y-m-d'));
        } catch (\Exception $e) {
            error_log('Failed to parse date: ' . $post->schemaObject['validThrough']);
        }

        return $post;
    }

    /**
     * Populate the language object.
     */
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
        $this->data['lang']->today                       = __('today', 'municipio');
        $this->data['lang']->tomorrow                    = __('tomorrow', 'municipio');
        $this->data['lang']->expired                     = __('expired', 'municipio');
    }

    /**
     * Set the expired flag.
     */
    private function setExpired(): void
    {
        $this->data['expired'] = $this->isExpired();
    }

    /**
     * Get the validThrough list item value.
     *
     * @return string
     */
    public function getValidThroughListItemValue(): string
    {
        $validThroughTimeStamp = strtotime($this->data['post']->schemaObject['validThrough']);

        if (empty($validThroughTimeStamp)) {
            return $this->data['post']->schemaObject['validThrough'];
        }

        $daysUntilValidThrough = $validThroughTimeStamp - strtotime(date('Y-m-d'));
        $daysUntilValidThrough = floor($daysUntilValidThrough / (60 * 60 * 24));
        $daysUntilValidThrough = intval($daysUntilValidThrough);
        $value                 = $this->data['post']->schemaObject['validThrough'] . ' (' . $daysUntilValidThrough . ' ' . $this->data['lang']->days . ')';

        if ($daysUntilValidThrough === 0) {
            $value = $this->data['post']->schemaObject['validThrough'] . ' (' . $this->data['lang']->today . ')';
        } elseif ($daysUntilValidThrough === 1) {
            $value = $this->data['post']->schemaObject['validThrough'] . ' (' . $this->data['lang']->tomorrow . ')';
        } elseif ($this->isExpired()) {
            $value = $this->data['post']->schemaObject['validThrough'] . ' (' . $this->data['lang']->expired . ')';
        }

        return $value;
    }

    /**
     * Check if the job posting is expired.
     *
     * @return bool
     */
    private function isExpired(): bool
    {
        $validThroughTimeStamp = strtotime($this->data['post']->schemaObject['validThrough']);

        if (empty($validThroughTimeStamp)) {
            return false;
        }

        $daysUntilValidThrough = $validThroughTimeStamp - strtotime(date('Y-m-d'));
        $daysUntilValidThrough = floor($daysUntilValidThrough / (60 * 60 * 24));
        $daysUntilValidThrough = intval($daysUntilValidThrough);

        return $daysUntilValidThrough < 0;
    }

    /**
     * Populate the information list.
     */
    private function populateInformationList(): void
    {
        $this->data['informationList'] = [];

        if ($this->data['post']->schemaObject['validThrough'] ?? null) {
            $this->data['informationList'][] = [
                'label' => $this->data['lang']->validThrough,
                'value' => $this->getValidThroughListItemValue()
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
