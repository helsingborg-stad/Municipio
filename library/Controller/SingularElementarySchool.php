<?php

namespace Municipio\Controller;

/**
 * Class SingularElementarySchool
 */
class SingularElementarySchool extends \Municipio\Controller\Singular
{
    protected object $postMeta;
    public string $view = 'single-schema-elementary-school';

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
            'accordionListItems' => new School\ElementarySchool\AccordionListItemsGenerator($schema, $this->wpService),
            'sliderItems'        => new School\SliderItemsGenerator($schema, $wp_embed),
            'personsAttributes'  => new School\PersonComponentsAttributesGenerator($schema),
            'addresses'          => new School\AddressGenerator($schema, $this->wpService),
            'mapAttributes'      => new School\MapComponentAttributesGenerator($schema),
            'usps'               => new School\ElementarySchool\UspsGenerator($schema, $this->post->getId(), $this->wpService),
            'actions'            => new School\ActionsGenerator($schema),
            'contactPoints'      => new School\ContactpointsGenerator($schema),
            'events'             => new School\EventsGenerator($schema)
            ] as $key => $generator
        ) {
            $this->data[$key] = $generator->generate();
        }

        $this->data['embedVideo'] = function ($url) {
            global $wp_embed;
            return $wp_embed->run_shortcode('[embed]' . esc_url($url) . '[/embed]');
        };

        // Setup view labels
        foreach (
            [
            'uspsLabel'          => $this->wpService->_x('Quick facts', 'ElementarySchool', 'municipio'),
            'contactLabel'       => $this->wpService->_x('Contact us', 'ElementarySchool', 'municipio'),
            'addressLabel'       => $this->wpService->_x('Address', 'ElementarySchool', 'municipio'),
            'actionsLabel'       => sprintf($this->wpService->_x('Do you wish to apply to %s?', 'ElementarySchool', 'municipio'), $this->post->getTitle()),
            'contactPointsLabel' => $this->wpService->_x('Follow us on social media', 'ElementarySchool', 'municipio'),
            'eventsLabel'        => $this->wpService->_x('Events', 'ElementarySchool', 'municipio'),
            ] as $labelKey => $labelText
        ) {
            $this->data['lang']->{$labelKey} = $labelText;
        }
    }
}
