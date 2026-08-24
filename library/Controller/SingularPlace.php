<?php

namespace Municipio\Controller;

use Municipio\Controller\SingularPlace\GetPlaceActions;
use Municipio\Controller\SingularPlace\GetPlaceInfoList;

/**
 * Class SingularPlace
 *
 * Used to represent physical places.
 */
class SingularPlace extends \Municipio\Controller\Singular
{
    public string $view = 'single-schema-place';

    public function init()
    {
        parent::init();

        $pageID = $this->getPageID();

        $this->data['relatedPosts'] = $this->getRelatedPosts($pageID);
        $this->data['placeInfoList'] = $this->createPlaceInfoList();
        $this->data['placeActions'] = $this->createActions();
    }

    private function createActions(): array
    {
        $placeActions = GetPlaceActions::getPlaceActions($this->post);
        return $this->wpService->applyFilters('Municipio/Controller/SingularPlace/actions', $placeActions, $this->post);
    }

    private function createPlaceInfoList(): array
    {
        $placeInfoList = GetPlaceInfoList::getPlaceInfoList($this->post);
        return $this->wpService->applyFilters('Municipio/Controller/SingularPlace/placeInfoList', $placeInfoList, $this->post);
    }
}
