<?php

namespace Municipio\Customizer;

abstract class PanelSection
{
    public string $id              = '';
    public int $priority           = 160;
    public string $panel           = '';
    public string $capability      = '';
    public array $themeSupports    = [];
    public string $title           = '';
    public string $description     = '';
    public string $type            = '';
    public string $previewUrl      = '';
    public bool $descriptionHidden = false;
    public array $sections         = [];
    public $activeCallback         = null;
    public $fieldsCallback         = null;
    public array $tabs             = [];

    public static function create()
    {
        $class = get_called_class();
        return new $class();
    }

    public function setID(string $id)
    {
        $this->id = $id;
        return $this;
    }

    public function getID(): string
    {
        return $this->id;
    }

    public function setFieldsCallback(callable $fieldsCallback)
    {
        $this->fieldsCallback = $fieldsCallback;
        return $this;
    }

    public function invokeFieldsCallback()
    {
        call_user_func($this->fieldsCallback);
        return $this;
    }

    public function setPriority(int $priority): PanelSection
    {
        $this->priority = $priority;
        return $this;
    }

    public function getPriority(): string
    {
        return $this->priority;
    }

    public function setThemeSupports(array $themeSupports): PanelSection
    {
        $this->themeSupports = $themeSupports;
        return $this;
    }

    public function getThemeSupports(): array
    {
        return $this->themeSupports ?? [];
    }

    public function setTitle(string $title): PanelSection
    {
        $this->title = $title;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setDescription(string $description): PanelSection
    {
        $this->description = $description;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setType(string $type): PanelSection
    {
        $this->type = $type;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setActiveCallback(callable $activeCallback): PanelSection
    {
        $this->activeCallback = $activeCallback;
        return $this;
    }

    public function getActiveCallback(): callable
    {
        return $this->activeCallback ?? fn() => true;
    }

    public function setCapability(string $capability): PanelSection
    {
        $this->capability = $capability;
        return $this;
    }

    public function getCapability(): string
    {
        return $this->capability;
    }

    public function setDescriptionHidden(bool $descriptionHidden): PanelSection
    {
        $this->descriptionHidden = $descriptionHidden;
        return $this;
    }

    public function getDescriptionHidden(): bool
    {
        return $this->descriptionHidden;
    }

    public function setPreviewUrl(string $previewUrl): PanelSection
    {
        $this->previewUrl = $previewUrl;
        return $this;
    }

    public function getPreviewUrl(): bool
    {
        return $this->previewUrl;
    }

    public function setPanel(string $panel): PanelSection
    {
        $this->panel = $panel;
        return $this;
    }

    public function getPanel(): string
    {
        return $this->panel;
    }

    public function getTabs(): array
    {
        return $this->tabs;
    }

    public function setTabs(array $tabs): PanelSection
    {
        $this->tabs = $tabs;
        return $this;
    }

    public function addSection(PanelSection $section): PanelSection
    {
        $this->sections[] = $section;
        return $this;
    }

    public function register(): PanelSection
    {
        $this->handleRegistration();
        $this->fieldsCallback && $this->invokeFieldsCallback();
        do_action('municipio_customizer_section_registered', $this);
        return $this;
    }

    abstract public function handleRegistration(): PanelSection;
}
