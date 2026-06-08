<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\MunicipioAuth\controller\secure;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class JWTStrategyTest extends TestCase
{
    private function skipIfJwtLibraryMissing(): void
    {
        if (!class_exists('Firebase\\JWT\\JWT')) {
            $this->markTestSkipped('firebase/php-jwt is not installed in this environment.');
        }
    }

    #[TestDox('should encode and decode JWT correctly')]
    public function testEncodeDecode(): void
    {
        $this->skipIfJwtLibraryMissing();

        $config = $this->createMock(SecureMunicipioAuthConfigInterface::class);
        $config->method('isValid')->willReturn(true);
        $config->method('expires')->willReturn(20 * 60);
        $config->method('getJWTKey')->willReturn('secret_key_that_should_be_long_enough');
        $config->method('getJWTHeaders')->willReturn(['iss' => 'test_issuer']);

        $strategy = new JWTStrategy();
        $payload = ['user_id' => 123, 'role' => 'admin'];
        $jwt = $strategy->encode($payload, $config);

        $decoded = $strategy->tryDecode($jwt, $config);
        static::assertNotNull($decoded);
        static::assertSame(123, $decoded['user_id']);
        static::assertSame('admin', $decoded['role']);
    }

    #[TestDox('verifies JWT headers and returns null if they do not match')]
    public function testInvalidHeaders(): void
    {
        $this->skipIfJwtLibraryMissing();

        $encodeConfig = $this->createMock(SecureMunicipioAuthConfigInterface::class);
        $encodeConfig->method('isValid')->willReturn(true);
        $encodeConfig->method('expires')->willReturn(20 * 60);
        $encodeConfig->method('getJWTKey')->willReturn('secret_key_that_should_be_long_enough');
        $encodeConfig->method('getJWTHeaders')->willReturn(['iss' => 'expected_issuer']);

        $decodeConfig = $this->createMock(SecureMunicipioAuthConfigInterface::class);
        $decodeConfig->method('isValid')->willReturn(true);
        $decodeConfig->method('expires')->willReturn(20 * 60);
        $decodeConfig->method('getJWTKey')->willReturn('secret_key_that_should_be_long_enough');
        $decodeConfig->method('getJWTHeaders')->willReturn(['iss' => 'different_issuer']);

        $strategy = new JWTStrategy();
        $payload = ['user_id' => 123, 'role' => 'admin'];
        $jwt = $strategy->encode($payload, $encodeConfig);

        $decoded = $strategy->tryDecode($jwt, $decodeConfig);
        static::assertNull($decoded);
    }

    #[TestDox('should return null for invalid JWT')]
    public function testInvalidJWT(): void
    {
        $this->skipIfJwtLibraryMissing();

        $config = $this->createMock(SecureMunicipioAuthConfigInterface::class);
        $config->method('isValid')->willReturn(true);

        $strategy = new JWTStrategy();
        $invalidJwt = 'invalid.jwt.token';
        $decoded = $strategy->tryDecode($invalidJwt, $config);
        static::assertNull($decoded);
    }
}
