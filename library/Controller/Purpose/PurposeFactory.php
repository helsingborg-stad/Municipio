<?php

namespace Municipio\Controller\Purpose;

class PurposeFactory
{
    public string $key;
    public string $label;
    public array $secondaryPurpose;
    protected string $view;

    public function __construct(string $key, string $label, array $secondaryPurpose = [])
    {
        $this->key              = $key;
        $this->label            = $label;
        $this->secondaryPurpose = $secondaryPurpose;
        $this->view             = "purpose-{$key}";

        self::initSecondaryPurposes();
    }
    /**
     * This method is empty by default and can be overridden by subclasses to add their own initialization logic.
     */
    public function init(): void
    {
    }

    protected function initSecondaryPurposes()
    {
        if (!empty($this->getSecondaryPurpose())) {
            foreach ($this->getSecondaryPurpose() as $className) {
                $instance = new $className();
                $instance->init();
            }
        }
    }
    public function getLabel(): string
    {
        return $this->label;
    }
    public function getKey(): string
    {
        return $this->key;
    }
    public function getView(): string
    {
        return $this->view;
    }
    public function getSecondaryPurpose(): array
    {
        return $this->secondaryPurpose;
    }
}
