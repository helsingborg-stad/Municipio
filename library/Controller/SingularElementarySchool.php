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

        // Setup view data
        foreach (
            [
            'preamble'           => new SingularElementarySchool\PreambleGenerator($this->post->getSchema()),
            'accordionListItems' => new SingularElementarySchool\AccordionListItemsGenerator($this->post->getSchema()),
            'sliderImages'       => new SingularElementarySchool\SliderImagesGenerator($this->post->getSchema()),
            'personsAttributes'  => new SingularElementarySchool\PersonComponentsAttributesGenerator($this->post->getSchema()),
            'address'            => new SingularElementarySchool\AddressGenerator($this->post->getSchema()),
            'mapAttributes'      => new SingularElementarySchool\MapComponentAttributesGenerator($this->post->getSchema()),
            'usps'               => new SingularElementarySchool\UspsGenerator($this->post->getSchema(), $this->post->getId(), $this->wpService),
            'actions'            => new SingularElementarySchool\ActionsGenerator($this->post->getSchema()),
            ] as $key => $generator
        ) {
            $this->data[$key] = $generator->generate();
        }

        // Setup view labels
        foreach (
            [
            'uspsLabel'    => $this->wpService->_x('Quick facts', 'ElementarySchool', 'municipio'),
            'contactLabel' => $this->wpService->_x('Contact us', 'ElementarySchool', 'municipio'),
            'addressLabel' => $this->wpService->_x('Address', 'ElementarySchool', 'municipio'),
            ] as $labelKey => $labelText
        ) {
            $this->data['lang']->{$labelKey} = $labelText;
        }
    }
}
