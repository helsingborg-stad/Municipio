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

        $this->data['category']   = $this->implodeTerms($this->post->getTerms(['project_meta_category']));
        $this->data['technology'] = $this->implodeTerms($this->post->getTerms(['project_meta_technology']));
        $this->data['status']     = $this->implodeTerms($this->post->getTerms(['project_meta_status']));
        $this->data['department'] = $this->implodeTerms($this->post->getTerms(['project_department']));
        $this->data['budget']     = $this->post->getSchemaProperty('funding')['amount'] ?? null;

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
        $this->data['lang']->status     = __('Status', 'municipio');
        $this->data['lang']->department = __('Department', 'municipio');
        $this->data['lang']->transition = __('Transition', 'municipio');
        $this->data['lang']->categories = __('Categories', 'municipio');
        $this->data['lang']->contact    = __('Contact', 'municipio');
        $this->data['lang']->budget     = __('Estimated budget', 'municipio');
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
                'label' => $this->data['lang']->categories,
                'value' => $this->data['category']
            ];
        }

        if (!empty($this->data['technology'])) {
            $this->data['informationList'][] = [
                'label' => $this->data['lang']->transition,
                'value' => $this->data['technology']
            ];
        }

        if (!empty($this->post->getSchemaProperty('employee')['alternateName'])) {
            $this->data['informationList'][] = [
                'label' => $this->data['lang']->contact,
                'value' => [
                    $this->post->getSchemaProperty('employee')['alternateName'],
                    $this->getEmailLinkFromEmployee($this->post->getSchemaProperty('employee'))
                ]
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
     * Gets an email link from an employee.
     *
     * @param array $employee
     * @return string|null
     */
    private function getEmailLinkFromEmployee(array $employee): ?string
    {
        if ($employee['email'] === null) {
            return null;
        }

        $email = strtolower($employee['email']);

        return '<a href="mailto:' . $email . '">' . $email . '</a>';
    }

    /**
     * Implode WP_terms.
     *
     * @param \WP_Term[] $terms
     * @return string
     */
    public function implodeTerms(array $terms): string
    {
        return implode(', ', array_map(fn ($term) => $term->name, $terms));
    }
}
