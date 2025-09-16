<?php

namespace Municipio\Controller;

/**
 * Class SingularPreschool
 */
class SingularPreschool extends \Municipio\Controller\Singular
{
    protected object $postMeta;
    public string $view = 'single-schema-preschool';

    public function init()
    {
        parent::init();
        $schema = $this->post->getSchema();

        // Setup view data
        foreach (
            [
            'preamble'                   => new SingularPreschool\PreambleGenerator($schema),
            'getFeaturedImageAttributes' => new SingularPreschool\FeaturedImageAttributesGenerator($schema),
            'visitUs'                    => new SingularPreschool\VisitUsGenerator($schema, $this->wpService),
            'accordionListItems'         => new SingularPreschool\AccordionListItemsGenerator($schema, $this->wpService),
            'sliderItems'                => new SingularPreschool\SliderItemsGenerator($schema),
            'personsAttributes'          => new SingularPreschool\PersonComponentsAttributesGenerator($schema),
            'address'                    => new SingularPreschool\AddressGenerator($schema, $this->wpService),
            'mapAttributes'              => new SingularPreschool\MapComponentAttributesGenerator($schema),
            'usps'                       => new SingularPreschool\UspsGenerator($schema, $this->post->getId(), $this->wpService),
            'actions'                    => new SingularPreschool\ActionsGenerator($schema),
            'contactPoints'              => new SingularPreschool\ContactpointsGenerator($schema),
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
            ] as $labelKey => $labelText
        ) {
            $this->data['lang']->{$labelKey} = $labelText;
        }
    }
}
