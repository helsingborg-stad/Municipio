<?php

namespace Modularity\HooksRegistrar;

use PHPUnit\Framework\TestCase;

class HooksRegistrarTest extends TestCase
{
    /**
     * @testdox register() calls addHooks() on provided object
     */
    public function testRegisterCallsAddHooksOnProvidedObject()
    {
        $hookable       = $this->getHookableClass();
        $hooksRegistrar = new HooksRegistrar();

        $hooksRegistrar->register($hookable);

        $this->assertEquals(1, $hookable->nbrOfCalls);
    }

    private function getHookableClass(): Hookable
    {
        return new class implements Hookable {
            public int $nbrOfCalls = 0;

            public function addHooks(): void
            {
                $this->nbrOfCalls++;
            }
        };
    }
}
