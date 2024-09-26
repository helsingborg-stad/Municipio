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

        $this->data['progress'] = get_post_meta($this->data['post']->id, 'progress', true) ?? null;
        $this->data['image']    = $this->getImageContractOrUrl($this->data['post']->id ?? null);

        $this->data = $this->setTerms($this->data);
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
     * Sets the terms for the project.
     *
     * @param array $data
     * @return array
     */
    private function setTerms($data): array
    {
        $map = [
            'category'   => 'project_meta_category',
            'technology' => 'project_meta_technology',
            'status'     => 'project_meta_status',
            'department' => 'project_department'
        ];

        foreach ($map as $key => $taxonomy) {
            $terms      = wp_get_post_terms($data['post']->id, $taxonomy);
            $data[$key] = is_array($terms) ? $terms[0]->name : null;
        }

        return $data;
    }

    /**
     * Appends translated strings to the language object.
     */
    private function appendToLangObject(): void
    {
        $this->data['lang']->information  = __('Information', 'municipio');
        $this->data['lang']->status       = __('Status', 'municipio');
        $this->data['lang']->department   = __('Department', 'municipio');
        $this->data['lang']->category     = __('Category', 'municipio');
        $this->data['lang']->technologies = __('Technologies', 'municipio');
        $this->data['lang']->contact      = __('Contact', 'municipio');
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
    }
}
