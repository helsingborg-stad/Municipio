<?php

namespace BladeComponentLibrary\Component\Breadcrumb;

/**
 * Class Breadcrumb
 * @package BladeComponentLibrary\Component\Breadcrumb
 */
class Breadcrumb extends \BladeComponentLibrary\Component\BaseController  
{
    
    public function init() {
        //Extract array for eazy access (fetch only)
        extract($this->data);
		$this->compParams = ['list' => $list];
        $this->iconDirectives();
    }

	/**
	 * iconDirectives
	 * @return array
	 */
    public function iconDirectives(){
		//Adds icon directives
		if($this->compParams['list'] && is_array($this->compParams['list']) && !empty($this->compParams['list'])) {

			foreach($this->compParams['list'] as $key => $item) {
				if(!isset($this->data['list'][$key]['icon'])) {
					$this->data['list'][$key]['icon'] = ($key) ? "chevron_right" : "bookmark";
				}
			}
			return $this->data;
		}
	}
}