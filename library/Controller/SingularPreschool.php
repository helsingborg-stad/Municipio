<?php

namespace Municipio\Controller;

/**
 * Class SingularPreschool
 */
class SingularPreschool extends \Municipio\Controller\Singular
{
    protected object $postMeta;
    public string $view = 'single-schema-preschool';

    /**
     * Initialize the controller
     */
    public function init()
    {
        parent::init();
        $schema = $this->post->getSchema();
        global $wp_embed;

        // Setup view data
        foreach (
            [
            'preamble'           => new School\PreambleGenerator($schema),
            'visitUs'            => new School\Preschool\VisitUsGenerator($schema, $this->wpService),
            'accordionListItems' => new School\Preschool\AccordionListItemsGenerator($schema, $this->wpService),
            'sliderItems'        => new School\SliderItemsGenerator($schema, $wp_embed),
            'personsAttributes'  => new School\PersonComponentsAttributesGenerator($schema),
            'addresses'          => new School\AddressGenerator($schema, $this->wpService),
            'mapAttributes'      => new School\MapComponentAttributesGenerator($schema),
            'usps'               => new School\Preschool\UspsGenerator($schema, $this->post->getId(), $this->wpService),
            'actions'            => new School\ActionsGenerator($schema),
            'contactPoints'      => new School\ContactpointsGenerator($schema),
            'events'             => new School\EventsGenerator($schema),
            ] as $key => $generator
        ) {
            $this->data[$key] = $generator->generate();
        }

        // Setup view labels
        foreach (
            [
            'uspsLabel'          => $this->wpService->_x('Quick facts', 'Preschool', 'municipio'),
            'contactLabel'       => $this->wpService->_x('Contact us', 'Preschool', 'municipio'),
            'addressLabel'       => $this->wpService->_x('Address', 'Preschool', 'municipio'),
            'actionsLabel'       => sprintf($this->wpService->_x('Do you wish to apply to %s?', 'Preschool', 'municipio'), $this->post->getTitle()),
            'contactPointsLabel' => $this->wpService->_x('Follow us on social media', 'Preschool', 'municipio'),
            'eventsLabel'        => $this->wpService->_x('Events', 'Preschool', 'municipio'),
            ] as $labelKey => $labelText
        ) {
            $this->data['lang']->{$labelKey} = $labelText;
        }
    }
}
