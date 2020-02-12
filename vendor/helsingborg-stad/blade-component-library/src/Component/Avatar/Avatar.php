<?php

namespace BladeComponentLibrary\Component\Avatar;

/**
 * Class Avatar
 * @package BladeComponentLibrary\Component\Avatar
 */
class Avatar extends \BladeComponentLibrary\Component\BaseController
{
	public function init()
	{
		//Extract array for eazy access (fetch only)
		extract($this->data);

		$this->compParams = [
			'name' 		=> $name,
			'size' 		=> $size,
			'icon' 		=> $icon,
			'image' 	=> $image,
			'initials' 	=> ''
		];

		//Get initials, Create label, Set size and ClassList
		$this->createInitials();
		$this->createLabel();
		$this->setIconParams();
		$this->classList();

		//Reset - Decides how to switch between data inputs
		$this->renderMostImportant();
	}

	/**
	 * Create label
	 * @return array
	 */
	public function createLabel()
	{
		$this->data['label'] = ($this->compParams['name']) ? $this->compParams['name'] : "Unknown user";
		return $this->data['label'];
	}

	/**
	 * Class List
	 * @return array
	 */
	public function classList()
	{
		$this->data['classList'][] = ($this->compParams['size']) ?
			$this->getBaseClass() . "--size-" . $this->compParams['size'] : null;
		return $this->data['classList'];
	}

	/**
	 * Set icon size (depending on avatar size)
	 * @return array
	 */
	public function setIconParams()
	{
		if(!empty($this->compParams['icon'])) {
			return null;
		}

		$defaultSize = (!empty($this->compParams['icon']['size'])) ? $this->compParams['icon']['size'] :
			$this->compParams['size'];

		$this->data['icon']['name'] = (!empty($this->compParams['icon']['name'])) ?
			$this->compParams['icon']['name'] : null;

		$this->data['icon']['size'] = (!empty($this->compParams['icon']['size'])) ?
			$this->compParams['size'] : $defaultSize;

		return $this->data['icon'];
	}

	/**
	 * renderMostImportant
	 */
	public function renderMostImportant()
	{
		//Reset icon, image if initals set
		if ($this->data['initials']) {
			$this->data['image'] = null;
			$this->data['icon'] = null;
		}

		//Reset icon, initials if image set
		if ($this->data['image']) {
			$this->data['icon'] = null;
			$this->data['initials'] = null;
		}

		//Reset image, initials if icon set
		if ($this->data['icon']) {
			$this->data['image'] = null;
			$this->data['initials'] = null;
		}
	}

	/**
	 * Creating initials
	 * @return string|null
	 */
	public function createInitials()
	{
		if (!empty($this->compParams['name']) && empty($this->compParams['image']) && empty($this->compParams['icon']['name'])) {
			$nameParts = preg_split("/( |-|_)/", $this->compParams['name']);

			if (is_array($nameParts) && !empty($nameParts)) {
				$initials = array();
				foreach ($nameParts as $part) {
					$initials[] = substr($part, 0, 1);
				}

				$this->data['initials'] = strtoupper(implode("", $initials));
				return $this->data['initials'];
			}
		} else {
			$this->data['initials'] = null;
		}

		return null;
	}
}