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

        $this->data['displayFeaturedImage'] = false;
        $this->populateLanguageObject();
        $this->populateInformationList();
        $this->setExpired();
    }

    /**
     * Sanitize the validThrough date string.
     *
     * @param object $post
     * @return string|null
     */
    private function tryGetFormattedValidThrough(): ?string
    {
        if (empty($this->post->getSchemaProperty('validThrough'))) {
            return null;
        }

        try {
            $date = $this->post->getSchemaProperty('validThrough');
            if (is_null($date)) {
                return null;
            }
            return $date->format('Y-m-d');
        } catch (\Exception $e) {
            error_log('Failed to parse date: ' . $this->post->getSchemaProperty('validThrough'));
        }

        return null;
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
    public function getValidThroughListItemValue(?int $currentTimestamp = null): string
    {
        /** @var DateTime|null $validThrough */
        $validThrough = $this->post->getSchemaProperty('validThrough');

        if (empty($validThrough)) {
            return '';
        }

        $daysUntilValidThrough = $validThrough->getTimestamp() - strtotime(date('Y-m-d', $currentTimestamp));
        $daysUntilValidThrough = floor($daysUntilValidThrough / (60 * 60 * 24));
        $daysUntilValidThrough = intval($daysUntilValidThrough);
        $value                 = $validThrough->format('Y-m-d') . ' (' . $daysUntilValidThrough . ' ' . $this->data['lang']->days . ')';

        if ($daysUntilValidThrough === 0) {
            $value = $validThrough->format('Y-m-d') . ' (' . $this->data['lang']->today . ')';
        } elseif ($daysUntilValidThrough === 1) {
            $value = $validThrough->format('Y-m-d') . ' (' . $this->data['lang']->tomorrow . ')';
        } elseif ($this->isExpired($currentTimestamp)) {
            $value = $validThrough->format('Y-m-d') . ' (' . $this->data['lang']->expired . ')';
        }

        return $value;
    }

    /**
     * Check if the job posting is expired.
     *
     * @return bool
     */
    private function isExpired(?int $currentTimestamp = null): bool
    {
        /** @var \DateTime|null $validThrough */
        $validThrough = $this->post->getSchemaProperty('validThrough');

        if (empty($validThrough)) {
            return false;
        }

        $daysUntilValidThrough = $validThrough->getTimestamp() - strtotime(date('Y-m-d', $currentTimestamp));
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

        if ($this->post->getSchemaProperty('validThrough')) {
            $this->data['informationList'][] = [
                'label' => $this->data['lang']->validThrough,
                'value' => $this->getValidThroughListItemValue()
            ];
        }

        if ($this->post->getSchemaProperty('employmentType')) {
            $this->data['informationList'][] = [
                'label' => $this->data['lang']->employmentType,
                'value' => $this->post->getSchemaProperty('employmentType')
            ];
        }

        if ($this->post->getSchemaProperty('datePosted')) {
            $this->data['informationList'][] = [
                'label' => $this->data['lang']->datePosted,
                'value' => $this->post->getSchemaProperty('datePosted')?->format('Y-m-d')
            ];
        }

        $addressRegion   = $this->post->getSchemaProperty('employmentUnit')['address']['addressRegion'] ?? null;
        $addressLocality = $this->post->getSchemaProperty('employmentUnit')['address']['addressLocality'] ?? null;

        if ($addressRegion || $addressLocality) {
            $this->data['informationList'][] = [
                'label' => $this->data['lang']->organizationAddressLocality,
                'value' => join(', ', array_filter([$addressLocality, $addressRegion]))
            ];
        }

        if ($this->post->getSchemaProperty('employmentUnit')['name'] ?? null) {
            $this->data['informationList'][] = [
                'label' => $this->data['lang']->administration,
                'value' => $this->post->getSchemaProperty('employmentUnit')['name'] ?? null
            ];
        }

        if ($this->post->getSchemaProperty('@id')) {
            $this->data['informationList'][] = [
                'label' => $this->data['lang']->reference,
                'value' => $this->post->getSchemaProperty('@id')
            ];
        }
    }
}
