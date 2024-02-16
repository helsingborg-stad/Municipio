<?php

namespace Municipio\Controller\ContentType;

use Municipio\Helper\ContentType as ContentTypeHelper;

/**
 * Class Event
 *
 * Used to represent events such as concerts, exhibitions, etc.
 *
 * @package Municipio\Controller\ContentType
 */
class Event extends ContentTypeFactory implements ContentTypeComplexInterface
{
    /**
     * Constructor method the Event content type.
     */
    public function __construct()
    {
        $this->key   = 'event';
        $this->label = __('Event', 'municipio');

        $this->addSecondaryContentType(new Place());

        $this->schemaParams = $this->applySchemaParamsFilter();

        parent::__construct($this->key, $this->label);
    }
    /**
     * Add hooks for the Event content type.
     */
    public function addHooks(): void
    {
    }
    /**
     * addSecondaryContentType
     *
     * @param ContentTypeComponentInterface $contentType
     * @return void
     */
    public function addSecondaryContentType(ContentTypeComponentInterface $contentType): void
    {
        if (ContentTypeHelper::validateSimpleContentType($contentType, $this)) {
            $this->secondaryContentType[] = $contentType;
        }
    }
    /**
     * Returns an array of schema parameters for the Event content type.
     *
     * @return array The schema parameters.
     */
    protected function schemaParams(): array
    {
        $params = [
            'startDate' => [
                'schemaType' => 'Date',
                'label'      => __('Start date', 'municipio')
            ],
            'endDate'   => [
                'schemaType' => 'Date',
                'label'      => __('End date', 'municipio')
            ],
            'image'     => [
                'schemaType' => 'ImageObject',
                'label'      => __('Image', 'municipio')
            ],
            'offers'    => [
                'schemaType' => 'Offer',
                'label'      => __('Offers', 'municipio')
            ],
        ];
        foreach ($this->getSecondaryContentType() as $contentType) {
            switch ($contentType->getKey()) {
                case 'place':
                    $placeParams        = $contentType->getSchemaParams();
                    $params['location'] = $placeParams['geo'];
                    break;

                default:
                    break;
            }
        }

        return $params;
    }

    /**
     * Get the structured data for a legacy event.
     *
     * @param int $postId The ID of the event post.
     * @return array The structured data array.
     */
    public function legacyGetStructuredData(int $postId, $entity): ?array
    {

        if (empty($entity)) {
            return [];
        }
        $post = \Municipio\Helper\Post::preparePostObject(get_post($postId));

        $entity->name($post->postTitle);
        $entity->description($post->postExcerpt);

        $eventMeta = \Municipio\Helper\WP::getFields($postId);

        $occasions = $eventMeta['occasions_complete'] ?? [];
        if (!empty($occasions)) {
            $entity->startDate($occasions[0]['start_date']);
            $entity->endDate($occasions[0]['end_date']);
        }

        $imageUrl           = wp_get_attachment_url($eventMeta['_thumbnail_id'] ?? null);
        $eventData['image'] = [$imageUrl];

        $bookingLink = $eventMeta['booking_link'] ?? false;
        if ($bookingLink) {
            $entity->offers([
                '@type' => 'Offer',
                'name'  => 'ticket',
                'url'   => $bookingLink,
            ]);
        }

        return $entity->toArray();
    }
    /**
     * Get the schema entity for the Event content type.
     *
     * @param \Spatie\SchemaOrg\Graph $graph The schema graph.
     * @return mixed The schema entity.
     */
    protected function getSchemaEntity(\Spatie\SchemaOrg\Graph $graph)
    {
        return $graph->event(); // Return the specific schema entity for Event
    }
}
