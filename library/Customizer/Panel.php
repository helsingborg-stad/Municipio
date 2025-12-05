<?php

namespace Municipio\Customizer;

abstract class Panel
{
    public string $id           = '';
    public int $priority        = 160;
    public string $capability   = '';
    public array $themeSupports = [];
    public string $title        = '';
    public string $description  = '';
    public string $type         = '';
    public array $sections      = [];
    public $activeCallback      = null;
    public string $panel        = '';
    public array $subPanels     = [];

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


    public function setPriority(int $priority): Panel
    {
        $this->priority = $priority;
        return $this;
    }

    public function getPriority(): string
    {
        return $this->priority;
    }

    public function setThemeSupports(array $themeSupports): Panel
    {
        $this->themeSupports = $themeSupports;
        return $this;
    }

    public function getThemeSupports(): array
    {
        return $this->themeSupports ?? [];
    }

    public function setTitle(string $title): Panel
    {
        $this->title = $title;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setDescription(string $description): Panel
    {
        $this->description = $description;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setType(string $type): Panel
    {
        $this->type = $type;
        return $this;
    }

    public function getType(): string
    {
        return !empty($this->type) ? $this->type : 'default';
    }

    public function setActiveCallback(callable $activeCallback): Panel
    {
        $this->activeCallback = $activeCallback;
        return $this;
    }

    public function getActiveCallback(): callable
    {
        return $this->activeCallback ?? fn() => true;
    }

    public function setCapability(string $capability): Panel
    {
        $this->capability = $capability;
        return $this;
    }

    public function getCapability(): string
    {
        return $this->capability;
    }

    public function setPanel(string $panel): Panel
    {
        $this->panel = $panel;
        return $this;
    }

    public function getPanel(): string
    {
        return $this->panel;
    }

    public function addSubPanels(Panel $subPanels): Panel
    {

        foreach ($subPanels as $subPanel) {
            $this->addSubPanel($subPanel);
        }

        return $this;
    }

    public function addSubPanel(Panel $subPanel): Panel
    {
        $subPanel->setPanel($this->getID())->register();
        $this->subPanels[] = $subPanel;
        return $this;
    }

    /**
     * @return Panel[]
     */
    public function getSubPanels(): array
    {
        return $this->subPanels;
    }

    public function addSections(array $sections): Panel
    {
        foreach ($sections as $section) {
            if (is_a($section, 'Municipio\Customizer\PanelSection')) {
                $this->addSection($section);
                continue;
            }
        }
        return $this;
    }

    public function addSection(PanelSection $section): Panel
    {

        if (empty($section->getPanel())) {
            $section->setPanel($this->getID());
        }

        $this->sections[] = $section;
        $section->register();
        return $this;
    }

    /**
     * @return PanelSection[]
     */
    public function getSections(): array
    {
        return $this->sections;
    }

    public function register(): Panel
    {
        $this->handleRegistration();
        do_action('municipio_customizer_panel_registered', $this);
        return $this;
    }

    abstract public function handleRegistration(): bool;
}
