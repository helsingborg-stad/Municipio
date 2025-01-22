<?php

namespace Municipio\Helper\Nonce;

use PHPUnit\Framework\TestCase;
use WpService\Contracts\WpCreateNonce;
use WpService\Implementations\FakeWpService;

class NonceServiceTest extends TestCase
{
    private WpCreateNonce $wpService;

    protected function setUp(): void
    {
        $this->wpService = new FakeWpService(['wpCreateNonce' => fn($action) => $action, 'wpVerifyNonce' => false]);
    }

    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated()
    {
        $this->assertInstanceOf(NonceService::class, new NonceService($this->wpService));
    }

    /**
     * @testdox getNonceField returns string
     */
    public function testGetNonceFieldReturnsString()
    {
        $nonceService = new NonceService($this->wpService);
        $this->assertIsString($nonceService->getNonceField());
    }

    /**
     * @testdox getNonceField returns a hidden input field
     */
    public function testGetNonceFieldReturnsAStringThatContainsANonceInputField()
    {
        $nonceService = new NonceService($this->wpService);
        $htmlString   = $nonceService->getNonceField();

        // Load the HTML string into a DOMDocument
        $dom = new \DOMDocument();
        @$dom->loadHTML($htmlString);

        // Get all input elements
        $input = $dom->getElementsByTagName('input')[0];

        $this->assertEquals('hidden', $input->getAttribute('type'));
    }

    /**
     * @testdox getNonceField returns a input field with the default name if no name is provided
     */
    public function testGetNonceFieldReturnsAInputFieldWithTheDefaultNameIfNoNameIsProvided()
    {
        $nonceService = new NonceService($this->wpService);
        $htmlString   = $nonceService->getNonceField();

        // Load the HTML string into a DOMDocument
        $dom = new \DOMDocument();
        @$dom->loadHTML($htmlString);

        // Get all input elements
        $input = $dom->getElementsByTagName('input')[0];

        $this->assertEquals(NonceService::DEFAULT_NAME, $input->getAttribute('name'));
    }

    /**
     * @testdox getNonceField returns a input field with the provided name
     */
    public function testGetNonceFieldReturnsAInputFieldWithTheProvidedName()
    {
        $nonceService = new NonceService($this->wpService);
        $htmlString   = $nonceService->getNonceField(null, 'custom_name');

        // Load the HTML string into a DOMDocument
        $dom = new \DOMDocument();
        @$dom->loadHTML($htmlString);

        // Get all input elements
        $input = $dom->getElementsByTagName('input')[0];

        $this->assertEquals('custom_name', $input->getAttribute('name'));
    }

    /**
     * @testdox getNonceField returns a input field with the default action if no action is provided
     */
    public function testGetNonceFieldReturnsAInputFieldWithTheDefaultActionIfNoActionIsProvided()
    {
        $nonceService = new NonceService($this->wpService);
        $htmlString   = $nonceService->getNonceField();

        // Load the HTML string into a DOMDocument
        $dom = new \DOMDocument();
        @$dom->loadHTML($htmlString);

        // Get all input elements
        $input = $dom->getElementsByTagName('input')[0];

        $this->assertEquals(NonceService::DEFAULT_ACTION, $input->getAttribute('value'));
    }

    /**
     * @testdox printNonceField returns void
     */
    public function testPrintNonceFieldReturnsVoid()
    {
        $nonceService = new NonceService($this->wpService);
        $this->assertNull($nonceService->printNonceField());
    }

    /**
     * @testdox printNonceField prints a nonce input field
     */
    public function testPrintNonceFieldPrintsANonceInputField()
    {
        $nonceService = new NonceService($this->wpService);
        $this->expectOutputString('<input type="hidden" name="' . NonceService::DEFAULT_NAME . '" value="' . NonceService::DEFAULT_ACTION . '">');
        $nonceService->printNonceField();
    }

    /**
     * @testdox verifyNonceField returns bool
     */
    public function testVerifyNonceFieldReturnsBool()
    {
        $nonceService = new NonceService($this->wpService);
        $this->assertIsBool($nonceService->verifyNonceField());
    }

    /**
     * @testdox verifyNonceField returns result of wp_verify_nonce
     */
    public function testVerifyNonceFieldReturnsResultOfWpVerifyNonce()
    {
        $wpServiceReturning1     = new FakeWpService(['wpVerifyNonce' => fn() => 1]);
        $wpServiceReturning2     = new FakeWpService(['wpVerifyNonce' => fn() => 2]);
        $wpServiceReturningFalse = new FakeWpService(['wpVerifyNonce' => fn() => false]);


        $this->assertTrue((new NonceService($wpServiceReturning1))->verifyNonceField());
        $this->assertTrue((new NonceService($wpServiceReturning2))->verifyNonceField());
        $this->assertFalse((new NonceService($wpServiceReturningFalse))->verifyNonceField());
    }

    /**
     * @testdox getNonce returns string
     */
    public function testGetNonceReturnsString()
    {
        $nonceService = new NonceService($this->wpService);
        $this->assertIsString($nonceService->getNonce());
    }

    /**
     * @testdox getNonce returns a nonce with the default action name if no action is provided
     */
    public function testGetNonceReturnsANonceWithTheDefaultActionNameIfNoActionIsProvided()
    {
        $nonceService = new NonceService($this->wpService);
        $this->assertEquals(NonceService::DEFAULT_ACTION, $nonceService->getNonce());
    }

    /**
     * @testdox getNonce returns a nonce with the provided action name
     */
    public function testGetNonceReturnsANonceWithTheProvidedActionName()
    {
        $nonceService = new NonceService($this->wpService);
        $this->assertEquals('custom_action', $nonceService->getNonce('custom_action'));
    }
}
