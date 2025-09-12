<?php

namespace Municipio\Controller;

use Municipio\Controller\SingularElementarySchool\ViewDataGeneratorInterface;
use Municipio\Schema\BaseType;
use Municipio\Schema\ElementarySchool;

/**
 * Class SingularElementarySchool
 */
class SingularElementarySchool extends \Municipio\Controller\Singular
{
    protected object $postMeta;
    public string $view = 'single-schema-elementary-school';

    public function init()
    {
        parent::init();
        $schema = $this->post->getSchema();

        // Setup view data
        foreach (
            [
            'preamble'           => new SingularElementarySchool\PreambleGenerator($schema),
            'accordionListItems' => new SingularElementarySchool\AccordionListItemsGenerator($schema),
            'sliderImages'       => new SingularElementarySchool\SliderImagesGenerator($schema),
            'personsAttributes'  => new SingularElementarySchool\PersonComponentsAttributesGenerator($schema),
            'address'            => new SingularElementarySchool\AddressGenerator($schema, $this->wpService),
            'mapAttributes'      => new SingularElementarySchool\MapComponentAttributesGenerator($schema, $this->wpService),
            'usps'               => new SingularElementarySchool\UspsGenerator($schema, $this->post->getId(), $this->wpService),
            'actions'            => new SingularElementarySchool\ActionsGenerator($schema),
            'contactPoints'      => new SingularElementarySchool\ContactpointsGenerator($schema),
            ] as $key => $generator
        ) {
            $this->data[$key] = $generator->generate();
        }

        // Setup view labels
        foreach (
            [
            'uspsLabel'          => $this->wpService->_x('Quick facts', 'ElementarySchool', 'municipio'),
            'contactLabel'       => $this->wpService->_x('Contact us', 'ElementarySchool', 'municipio'),
            'addressLabel'       => $this->wpService->_x('Address', 'ElementarySchool', 'municipio'),
            'actionsLabel'       => sprintf($this->wpService->_x('Do you wish to apply to %s?', 'ElementarySchool', 'municipio'), $this->post->getTitle()),
            'contactPointsLabel' => $this->wpService->_x('Follow us on social media', 'ElementarySchool', 'municipio'),
            ] as $labelKey => $labelText
        ) {
            $this->data['lang']->{$labelKey} = $labelText;
        }
    }
}
