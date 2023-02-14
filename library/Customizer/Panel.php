<?php

namespace Municipio\Customizer;

abstract class Panel {

    public string $id;
    public array $args;
    
    /**
    * Constructor.
    *
    * @param string               $id      A specific ID for the panel.
    * @param array                $args    {
    *     Optional. Array of properties for the new Panel object. Default empty array.
    *
    *     @type int             $priority        Priority of the panel, defining the display order
    *                                            of panels and sections. Default 160.
    *     @type string          $capability      Capability required for the panel.
    *                                            Default `edit_theme_options`.
    *     @type mixed[]         $theme_supports  Theme features required to support the panel.
    *     @type string          $title           Title of the panel to show in UI.
    *     @type string          $description     Description to show in the UI.
    *     @type string          $type            Type of the panel.
    *     @type string          $panel           Parent panel.
    *     @type callable        $active_callback Active callback.
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

    public function getPanel():string {
        return $this->args['panel'] ?? '';
    }
    
    public function getArgs():array {
        return $this->args;
    }
    
    protected function register() {
        $this->handleRegistration();
        do_action('municipio_customizer_panel_registered', $this->id, $this->args);
    }
    
    abstract function handleRegistration():bool;
}