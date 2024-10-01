<?php

namespace Municipio\Controller;

use Municipio\Integrations\Component\ImageResolver;
use ComponentLibrary\Integrations\Image\Image as ImageComponentContract;

/**
 * Class SingularJobPosting
 */
class SingularProject extends \Municipio\Controller\Singular
{
    protected object $postMeta;
    public string $view = 'single-schema-project';

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();

        $this->data['progress'] = $this->data['post']->progress ?? null;
        $this->data['image']    = $this->getImageContractOrUrl($this->data['post']->id ?? null);

        $this->data['category']   = $this->implodeProjectTerms($this->getProjectTerm('category'));
        $this->data['technology'] = $this->implodeProjectTerms($this->getProjectTerm('technology'));
        $this->data['status']     = $this->implodeProjectTerms($this->getProjectTerm('status'));
        $this->data['department'] = $this->implodeProjectTerms($this->getProjectTerm('department'));
        $this->data['budget']     = $this->data['post']->schemaObject['funding']['amount'] ?? null;

        $this->appendToLangObject();
        $this->setInformationListData();
    }

    /**
     * Gets the image contract or url.
     *
     * @param int $postId
     *
     * @return null|string|ImageInterface
     */
    private function getImageContractOrUrl(?int $postId): null|string|ImageComponentContract
    {
        if (is_null($postId)) {
            return null;
        }

        if ($thumbnailId = get_post_thumbnail_id($postId)) {
            return ImageComponentContract::factory(
                (int) $thumbnailId,
                [768, false],
                new ImageResolver()
            );
        }

        return get_the_post_thumbnail_url($postId) ?: null;
    }


    /**
     * Appends translated strings to the language object.
     */
    private function appendToLangObject(): void
    {
        $this->data['lang']->status       = __('Status', 'municipio');
        $this->data['lang']->department   = __('Department', 'municipio');
        $this->data['lang']->category     = __('Transition', 'municipio');
        $this->data['lang']->technologies = __('Categories', 'municipio');
        $this->data['lang']->contact      = __('Contact', 'municipio');
        $this->data['lang']->budget       = __('Estimated budget', 'municipio');
    }

    /**
     * Sets the information list data for the project.
     */
    private function setInformationListData(): void
    {
        $this->data['informationList'] = [];

        if (!empty($this->data['department'])) {
            $this->data['informationList'][] = [
                'label' => $this->data['lang']->department,
                'value' => $this->data['department']
            ];
        }

        if (!empty($this->data['category'])) {
            $this->data['informationList'][] = [
                'label' => $this->data['lang']->category,
                'value' => $this->data['category']
            ];
        }

        if (!empty($this->data['technology'])) {
            $this->data['informationList'][] = [
                'label' => $this->data['lang']->technologies,
                'value' => $this->data['technology']
            ];
        }

        if (!empty($this->data['post']->schemaObject['employee']['alternateName'])) {
            $this->data['informationList'][] = [
                'label' => $this->data['lang']->contact,
                'value' => $this->data['post']->schemaObject['employee']['alternateName']
            ];
        }

        if (!empty($this->data['budget'])) {
            $this->data['informationList'][] = [
                'label' => $this->data['lang']->budget,
                'value' => $this->data['budget']
            ];
        }
    }

    /**
     * Gets a project term.
     *
     * @param string $key
     * @return array|null
     */
    private function getProjectTerm(string $key): ?array
    {
        return isset($this->data['post']->projectTerms[$key]) ? $this->data['post']->projectTerms[$key] : null;
    }

    /**
     * Implode project terms.
     *
     * @param array|null $termArray
     * @return string|null
     */
    private function implodeProjectTerms(?array $termArray): ?string
    {
        return $termArray ? implode(', ', $termArray) : null;
    }
}
