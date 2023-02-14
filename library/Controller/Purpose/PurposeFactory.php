<?php

namespace Municipio\Controller\Purpose;

class PurposeFactory
{
    protected string $view = '';
    protected string $key = '';
    protected string $label = '';

    protected array $secondaryPurpose = [];

    public function __construct()
    {
    }
    public function init()
    {
    }

    protected function initSecondaryPurpose()
    {
        if (!empty($this->secondaryPurpose)) {
            foreach ($this->secondaryPurpose as $className) {
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
