<?php

namespace Municipio\Customizer;

abstract class PanelSection {
    
    public string $id;
    public array $args;
    
    /**
     * Constructor.
     *
     * @param string               $id      A specific ID for the panel.
     * @param array                $args    {
     *     Optional. Array of properties for the new Panel object. Default empty array.
     *
     *     @type int             $priority           Priority of the section, defining the display order
     *                                               of panels and sections. Default 160.
     *     @type string          $panel              The panel this section belongs to (if any).
     *                                               Default empty.
     *     @type string          $capability         Capability required for the section.
     *                                               Default 'edit_theme_options'
     *     @type string|string[] $theme_supports     Theme features required to support the section.
     *     @type string          $title              Title of the section to show in UI.
     *     @type string          $description        Description to show in the UI.
     *     @type string          $type               Type of the section.
     *     @type string          $preview_url        Preview Url.
     *     @type callable        $active_callback    Active callback.
     *     @type bool            $description_hidden Hide the description behind a help icon,
     *                                               instead of inline above the first control.
     *                                               Default false.
     * }
     */
    public function __construct(string $id, array $args = []) {
        $this->id = $id;
        $this->args = $args;
        $this->register();
    }
    
    public function getID():string {
        return $this->id;
    }
    
    public function getPriority():string {
        return $this->args['priority'] ?? 160;
    }
    
    public function getThemeSupports():array {
        return $this->args['theme_supports'] ?? [];
    }
    
    public function getTitle():string {
        return $this->args['title'] ?? '';
    }
    
    public function getDescription():string {
        return $this->args['description'] ?? '';
    }
    
    public function getType():string {
        return $this->args['type'] ?? 'default';
    }
    
    public function getActiveCallback():callable {
        return $this->args['type'] ?? fn() => true;
    }
    
    public function getCapability():string {
        return $this->args['capability'] ?? '';
    }

    public function getDescriptionHidden():bool {
        return $this->args['description_hidden'] ?? false;
    }
    
    public function getPreviewUrl():bool {
        return $this->args['preview_url'] ?? false;
    }
    
    public function getPanel():string {
        return $this->args['panel'] ?? '';
    }
    
    public function getArgs():array {
        return $this->args;
    }
    
    protected function register() {
        $this->handleRegistration();
        do_action('municipio_customizer_section_registered', $this->id, $this->args);
    }
    
    abstract function handleRegistration():PanelSection;
}