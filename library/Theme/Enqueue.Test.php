<?php

class EnqueueTest extends \PHPUnit\Framework\TestCase
{
    public function testIsDefined()
    {
        $this->assertTrue(class_exists('Municipio\Theme\Enqueue'));
    }

    public function testGetScriptDependenciesReturnsArrayOfStrings()
    {
        global $wp_scripts;
        $wp_scripts->registered = $this->buildRegisteredScriptsObject(['foo' => []]);
        $enqueue = new Municipio\Theme\Enqueue();
        $scripts = $enqueue->getScriptDependencies('foo');
        $this->assertIsArray($scripts);
        $this->assertContainsOnly('string', $scripts);
    }

    public function testGetScriptDependenciesIncludesScriptDependencies()
    {
        $wp_scripts = new stdClass();
        global $wp_scripts;
        $wp_scripts->registered = $this->buildRegisteredScriptsObject(['wp-i18n' => ['jquery']]);
        $enqueue = new Municipio\Theme\Enqueue();
        $handles = $enqueue->getScriptDependencies('wp-i18n');
        $this->assertContains('jquery', $handles);
    }

    public function testGetScriptDependenciesCallsThrowsIfScriptIsNotRegistered() {
        $wp_scripts = new stdClass();
        global $wp_scripts;
        $wp_scripts->registered = [];
        $enqueue = new Municipio\Theme\Enqueue();

        try {
            $enqueue->getScriptDependencies('foo', true);
        } catch (Exception $e) {
            $this->assertEquals('Script "foo" is not registered.', $e->getMessage());
            return;
        }
    }

    public function testGetScriptDependenciesIncludesScriptDependenciesMultipleLevelsUp()
    {
        $wp_scripts = new stdClass();
        global $wp_scripts;
        $wp_scripts->registered = $this->buildRegisteredScriptsObject(['wp-i18n' => ['jquery'], 'jquery' => ['jquery-core']]);
        $enqueue = new Municipio\Theme\Enqueue();
        $handles = $enqueue->getScriptDependencies('wp-i18n');
        $this->assertContains('jquery-core', $handles);
    }

    private function buildRegisteredScriptsObject(array $scripts)
    {
        $registered = [];

        foreach ($scripts as $handle => $deps) {
            $registered[$handle] = (object) [
                'deps' => $deps,
            ];
        }

        return $registered;
    }
}
