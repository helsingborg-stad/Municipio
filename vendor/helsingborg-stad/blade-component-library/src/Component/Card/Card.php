<?php

namespace BladeComponentLibrary\Component\Card;

/**
 * Class Card
 * @package BladeComponentLibrary\Component\Card
 */
class Card extends \BladeComponentLibrary\Component\BaseController
{

	public function init()
	{
		//Extract array for eazy access (fetch only)
		extract($this->data);

		// Encapsulates component data expose it globally for class
		$this->compParams = [
			'title' 	=> $title,
			'content' 	=> $content,
			'byline' 	=> $byline,
			'dropdown' 	=> $dropdown,
			'accordion' => $accordion,
			'icons' 	=> $icons,
			'avatar' 	=> $avatar,
			'buttons' 	=> $buttons,
			'top' 		=> $top,
			'bottom' 	=> $bottom,
			'href' 		=> $href,
            'hasRipple' => $hasRipple,
            'dark_background' => $dark_background
		];

		// Check if Different view parts in use
		$this->setViewContainers();

		// Set ClassList for component
		$this->setClassListParameters();

		// Set Avatar parameters
		$this->setAvatarParameters();

		// Set Text parameters for title, byline, content
		$this->setTextParameters();

		// Set Icon parameters
		$this->setIconParameters();

		// Set Button parameters
		$this->setButtonParameters();

		// Set Dropdown parameters
		$this->setDropDownParameters();

		// Set Dropdown parameters
		$this->setAccordionParameters();
	}


	/**
	 * View containers such as Body and Footer
	 * @return array
	 */
	public function setViewContainers()
	{
		$this->data['showBody'] = (!empty(array_filter([$this->compParams['title'], $this->compParams['content'],
			$this->compParams['byline']]))) ? true : false;

		$this->data['showFooter'] = (!empty(array_filter([$this->compParams['buttons']])) ||
			!empty(array_filter([$this->compParams['icons']])) || !empty($this->compParams['bottom']) ||
			!empty($this->compParams['dropdown'])) ? true : false;

		$this->data['top'] = (isset($this->compParams['top']) && !empty($this->compParams['top'])) ? 
            $this->compParams['top'] : null;
		$this->data['bottom'] = (isset($this->compParams['top']) && !empty($this->compParams['bottom'])) ?
            $this->compParams['bottom'] : null;
	}


	/**
	 * ClassList parameters
	 * @return array
	 */
	public function setClassListParameters()
	{
        $this->data['classList'][] = ($this->compParams['href']) ? $this->getBaseClass() . "--link" : '';
        
        if ($this->compParams['dark_background']) {
            $this->data['classList'][] = $this->getBaseClass() . "__background--dark";
        }

		if ($this->compParams['hasRipple']) {
			$this->data['classList'][] = "ripple";
			$this->data['classList'][] = "ripple--before";
		}

	}


	/**
	 * Avatar parameters
	 * @return array
	 */
	public function setAvatarParameters()
	{
		$this->data['avatarImage'] = (isset($this->compParams['avatar']['image']) &&
			!empty($this->compParams['avatar']['image'])) ? $this->compParams['avatar']['image'] : null;

		$this->data['avatarName'] = (isset($this->compParams['avatar']['name']) &&
			!empty($this->compParams['avatar']['name'])) ? $this->compParams['avatar']['name'] : null;

	}


	/**
	 * Text Parameters
	 * @return array
	 */
	public function setTextParameters()
	{
		$this->data['byline']['position'] = (!empty($this->compParams['byline']['position'])) ?
			$this->compParams['byline']['position'] : null;

		$this->data['byline']['text'] = (!empty($this->compParams['byline']['position'])) ?
			$this->compParams['byline']['text'] : null;

		$this->data['title']['position'] = (!empty($this->compParams['title']['position'])) ?
			$this->compParams['title']['position'] : null;

		$this->data['title']['text'] = (!empty($this->compParams['title']['text'])) ?
			$this->compParams['title']['text'] : null;

		$this->data['content'] = (!empty($this->compParams['content'])) ?
			$this->compParams['content'] : null;

	}


	/**
	 * Icon Parameteters
	 * @return array
	 */
	public function setIconParameters()
	{
		$this->data['icons'] = (!empty(array_filter($this->compParams['icons']))) ? $this->compParams['icons'] : false;

		if ($this->data['icons']) {
			foreach ($this->data['icons'] as $key => $iconParams) {
				$this->data['icons'][$key]['color'] = array_key_exists('color', $iconParams) ?
					$this->data['icons'][$key]['color'] : null;

				$this->data['icons'][$key]['size'] = array_key_exists('size', $iconParams) ?
					$this->data['icons'][$key]['size'] : null;

				$this->data['icons'][$key]['classList'] = array_key_exists('classList', $iconParams) ?
					$this->data['icons'][$key]['classList'] : [];

				$this->data['icons'][$key]['attributeList'] = array_key_exists('attributeList', $iconParams) ?
					$this->data['icons'][$key]['attributeList'] : [];
			}
		}
	}


	/**
	 * Dropdown parameters
	 * @return array
	 */
	public function setDropDownParameters()
	{
		if (!empty(array_filter($this->compParams['dropdown']))) {
			$this->data['dropdown']["position"] = (!empty($this->compParams['dropdown']['position'])) ?
				$this->compParams['dropdown']['position'] : null;

			$this->data['dropdown']["direction"] = (!empty($this->compParams['dropdown']['direction'])) ?
				$this->compParams['dropdown']['direction'] : null;

			$this->data['dropdown']["items"] = (!empty(array_filter($this->compParams['dropdown']['items']))) ?
				$this->compParams['dropdown']['items'] : null;

			foreach ($this->data['dropdown']["items"] as $keyDrop => $dropParams) {
				$this->data['dropdown']['text'] = (!empty($dropParams[$keyDrop]['text'])) ?
					$this->data['dropdown']['text'] : null;

				$this->data['dropdown']['link'] = (!empty($dropParams[$keyDrop]['link'])) ?
					$this->data['dropdown']['link'] : null;
			}
		}
	}


	/**
	 * Button parameters
	 * @return array
	 */
	public function setButtonParameters()
	{
		$this->data['buttons'] = (!empty(array_filter($this->compParams['buttons']))) ?
			$this->compParams['buttons'] : false;
		if ($this->data['buttons']) {
			foreach ($this->data['buttons'] as $key => $buttonParams) {
				$this->data['buttons'][$key]['href'] = array_key_exists('href', $buttonParams) ?
					$this->data['buttons'][$key]['href'] : null;

				$this->data['buttons'][$key]['text'] = array_key_exists('text', $buttonParams) ?
					$this->data['buttons'][$key]['text'] : null;

				$this->data['buttons'][$key]['name'] = array_key_exists('name', $buttonParams) ?
					$this->data['buttons'][$key]['name'] : null;

				$this->data['buttons'][$key]['color'] = array_key_exists('color', $buttonParams) ?
					$this->data['buttons'][$key]['color'] : '';

                $this->data['buttons'][$key]['isTextButton'] = array_key_exists('isTextButton', $buttonParams) ?
                    $this->data['buttons'][$key]['isTextButton'] : false;
			}
		}
	}

	/**
	 * Accordion parameters
	 * @return array
	 */
	public function setAccordionParameters()
	{
		if (!empty(array_filter($this->compParams['accordion']))) {

			$this->data['accordion']["items"] = (!empty(array_filter($this->compParams['accordion']['items']))) ?
				$this->compParams['accordion']['items'] : null;

			$this->data['accordion']["classList"] = (!empty($this->compParams['accordion']['classList'])) ?
				$this->compParams['accordion']['classList'] : null;

			foreach ($this->data['accordion']["items"] as $keyInt => $accordionParams) {
				$this->data['accordion']['heading'] = (!empty($accordionParams[$keyInt]['heading'])) ?
					$this->data['accordion']['heading'] : null;

				$this->data['accordion'][$keyInt]['content'] = (!empty($accordionParams[$keyInt]['content'])) ?
					$this->data['accordion'][$keyInt]['content'] : null;
			}
		}
	}
}
