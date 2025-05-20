<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\NonceValidation;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\WpVerifyNonce;

class UpdatePostNonceValidatorServiceTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated(): void
    {
        $postNonceValidatorService = new UpdatePostNonceValidatorService($this->getWpVerifyNonceMock(true));

        $this->assertInstanceOf(UpdatePostNonceValidatorService::class, $postNonceValidatorService);
    }

    /**
     * @testdox isValid() returns true when nonce is valid
     */
    public function testIsValidReturnsTrueWhenNonceIsValid(): void
    {
        $postId = 123;
        $nonce  = 'valid_nonce';

        $wpVerifyNonceMock = $this->getWpVerifyNonceMock(true);
        $wpVerifyNonceMock->expects($this->once())->method('wpVerifyNonce')->with($nonce, "update-post_{$postId}")->willReturn(1);
        $postNonceValidatorService = new UpdatePostNonceValidatorService($wpVerifyNonceMock);

        $result = $postNonceValidatorService->isValid($postId, $nonce);

        $this->assertEquals(true, $result);
    }

    /**
     * @testdox isValid() returns false when nonce is invalid
     */
    public function testIsValidReturnsFalseWhenNonceIsInvalid(): void
    {
        $postId = 123;
        $nonce  = 'invalid_nonce';

        $wpVerifyNonceMock = $this->getWpVerifyNonceMock(false);
        $wpVerifyNonceMock->expects($this->once())->method('wpVerifyNonce')->with($nonce, "update-post_{$postId}")->willReturn(false);
        $postNonceValidatorService = new UpdatePostNonceValidatorService($wpVerifyNonceMock);

        $result = $postNonceValidatorService->isValid($postId, $nonce);

        $this->assertEquals(false, $result);
    }

    private function getWpVerifyNonceMock(bool $returnValue): WpVerifyNonce|MockObject
    {
        return $this->createMock(WpVerifyNonce::class);
    }
}
