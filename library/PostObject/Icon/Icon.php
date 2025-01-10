<?php

namespace Municipio\PostObject\Icon;

/**
 * Icon class that adhere to the Icon component atttibutes.
 */
class Icon implements IconInterface
{
    /**
     * @inheritDoc
     */
    public function getSize(): string
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getLabel(): string
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getIcon(): string
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getColor(): string
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getCustomColor(): string
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getComponentElement(): string
    {
        return 'span';
    }

    /**
     * @inheritDoc
     */
    public function getFilled(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getDecorative(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'size'             => $this->getSize(),
            'label'            => $this->getLabel(),
            'icon'             => $this->getIcon(),
            'color'            => $this->getColor(),
            'customColor'      => $this->getCustomColor(),
            'componentElement' => $this->getComponentElement(),
            'filled'           => $this->getFilled(),
            'decorative'       => $this->getDecorative(),
        ];
    }
}
