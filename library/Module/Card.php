<?php

namespace Municipio\Module;

class Card extends \Modularity\Module
{

    /**
     * Module args
     * @var array
     */
    public $args = array(
        'id' => 'card',
        'nameSingular' => 'Card',
        'namePlural' => 'Cards',
        'description' => 'Content introduction cards',
        'supports' => array(),
        'icon' => 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pg0KPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDE2LjAuMCwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDYuMDAgQnVpbGQgMCkgIC0tPg0KPCFET0NUWVBFIHN2ZyBQVUJMSUMgIi0vL1czQy8vRFREIFNWRyAxLjEvL0VOIiAiaHR0cDovL3d3dy53My5vcmcvR3JhcGhpY3MvU1ZHLzEuMS9EVEQvc3ZnMTEuZHRkIj4NCjxzdmcgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB4PSIwcHgiIHk9IjBweCINCgkgd2lkdGg9IjMxNS45NDRweCIgaGVpZ2h0PSIzMTUuOTQzcHgiIHZpZXdCb3g9IjAgMCAzMTUuOTQ0IDMxNS45NDMiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDMxNS45NDQgMzE1Ljk0MzsiDQoJIHhtbDpzcGFjZT0icHJlc2VydmUiPg0KPGc+DQoJPGc+DQoJCTxwYXRoIGQ9Ik0xOTguNTg3LDIwNi43MDZjMC4yMzEsMC4wMzEsMC40NjQsMC4wNDcsMC42OTYsMC4wNDdjMC43NzQsMCwxLjUzOS0wLjE2OCwyLjI0Ni0wLjQ5bDExMS4yNTQtNTAuNzQ2DQoJCQljMi4zMDUtMS4wNDcsMy41NjQtMy41NTIsMy4wNDMtNi4wMzNjLTAuNTI3LTIuNDczLTIuNy00LjI1My01LjIzMS00LjI4MmMtMC45MDctMC4wMTEtOTAuOTg4LTEuOTI4LTEzMC45NjEtODEuODE1DQoJCQljLTMuNzc1LTcuNTM5LTEzLjA2Ny0xMi4wNDItMjEuMDk3LTEwLjExNUw2NS42NzUsNzUuNjQ2Yy00LjQ4MiwxLjA4MS04LjA1MiwzLjkzNy05Ljc5LDcuODMxDQoJCQljLTEuNzAxLDMuODA3LTEuNDYzLDguMjM0LDAuNjQ4LDEyLjE0OUM3Mi42NjMsMTI1LjU3OSwxMTguOCwxOTYuMzQ5LDE5OC41ODcsMjA2LjcwNnogTTY1LjczOSw4Ny44ODINCgkJCWMwLjM2NC0wLjgxLDEuMjYtMS40NDgsMi40NjMtMS43MzJsOTIuODY1LTIyLjM3M2MwLjQ1OS0wLjExMywwLjk0OS0wLjE2OSwxLjQ1LTAuMTY5YzIuOTg0LDAsNi4xMTcsMS45NDMsNy40NTYsNC42MTcNCgkJCWMzMC45ODcsNjEuOTIzLDg5LjgxNyw4MC4xOCwxMjAuNjA0LDg1LjU1NGwtOTIuMTIxLDQyLjAyMWMtNzMuOTE1LTEwLjQyMS0xMTcuMTkyLTc2Ljk5OC0xMzIuNDMtMTA1LjI5DQoJCQlDNjUuNjcsODkuODMxLDY1LjMyNSw4OC44MjEsNjUuNzM5LDg3Ljg4MnoiLz4NCgkJPHJlY3QgeT0iMjUxLjcyNiIgd2lkdGg9IjE5Ni4zMzEiIGhlaWdodD0iMTEuMzg1Ii8+DQoJCTxwb2x5Z29uIHBvaW50cz0iMzEwLjU0MiwyMTIuNzM5IDMxMC41NDIsMjAxLjM1OCAxOTkuMjYyLDI1MS43MjYgMTk5LjI2MiwyNjMuMTE2IAkJIi8+DQoJCTxwYXRoIGQ9Ik0zMTAuNTQyLDE5Ny4zODNoLTI4LjkzMWwtODMuMDE0LDM5LjYyNGwtMy42MTgsMC4wMTZjLTUwLjE5NSwwLTkwLjYyNC04Ljg4LTEyMC44NDktMTkuMzExTDAsMjQ3LjU4NmgxOTYuNjczDQoJCQlMMzEwLjU0MiwxOTcuMzgzeiIvPg0KCQk8cGF0aCBkPSJNMjEuMTQ2LDE4MS4zOThjMjEuNTc0LDEzLjYzMiw4MS45MjMsNDUuMzMxLDE3Ni4yMTcsNDQuNzg4bDEwOS44NzctNTIuNDQ1YzAsMC01LjkyMiwwLjAzMi0xNS42MjUtMC4yNTNsLTkzLjg5NCw0Mi44MzENCgkJCWwtMS41MzQtMC4yYy01My42ODctNi45NzItOTIuMDI3LTQxLjIxMi0xMTYuMTA1LTcxLjU0MmwtNTcuOTk3LDI2LjI1OUMxNi42NTQsMTczLjI5OSwxNi4xMDMsMTc4LjIxNCwyMS4xNDYsMTgxLjM5OHoiLz4NCgk8L2c+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8L3N2Zz4NCg=='
    );

    /**
     * Constructor
     */
    public function __construct()
    {
        // This will register the module
        $this->register(
            $this->args['id'],
            $this->args['nameSingular'],
            $this->args['namePlural'],
            $this->args['description'],
            $this->args['supports'],
            $this->args['icon']
        );

        // Enqueue action
        add_action('Modularity/Module/' . $this->moduleSlug . '/enqueue', array($this, 'enqueueAssets'));

        // Add our template folder as search path for templates
        add_filter('Modularity/Module/TemplatePath', function ($paths) {
            $paths[] = get_template_directory() . '/templates/';
            return $paths;
        });
    }

    /**
     * Enqueue your scripts and/or styles with wp_enqueue_script / wp_enqueue_style
     * @return
     */
    public function enqueueAssets()
    {

    }
}
