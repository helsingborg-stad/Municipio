<?php

namespace Municipio\PostObject\Icon;

/**
 * Icon class that adhere to the Icon component atttibutes.
 */
class Icon implements IconInterface, IconFactoryInterface
{
    /**
     * Constructor.
     *
     * @param array $properties Icon properties. Default is empty array.
     */
    public function __construct(private array $properties = [])
    {
    }

    /**
     * @inheritDoc
     */
    public function getSize(): string
    {
        return $this->properties['size'] ?? 'md';
    }

    /**
     * @inheritDoc
     */
    public function getLabel(): string
    {
        return $this->properties['label'] ?? false;
    }

    /**
     * @inheritDoc
     */
    public function getIcon(): string
    {
        return $this->properties['icon'] ?? false;
    }

    /**
     * @inheritDoc
     */
    public function getColor(): string
    {
        return $this->properties['color'] ?? false;
    }

    /**
     * @inheritDoc
     */
    public function getCustomColor(): string
    {
        return $this->properties['customColor'] ?? false;
    }

    /**
     * @inheritDoc
     */
    public function getComponentElement(): string
    {
        return $this->properties['componentElement'] ?? 'span';
    }

    /**
     * @inheritDoc
     */
    public function getFilled(): bool
    {
        return $this->properties['filled'] ?? false;
    }

    /**
     * @inheritDoc
     */
    public function getDecorative(): bool
    {
        return $this->properties['decorative'] ?? false;
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

    /**
     * @inheritDoc
     */
    public static function create(array $properties = []): IconInterface
    {
        return new self($properties);
    }
}
