<?php

declare(strict_types=1);

namespace Municipio\KulturkortetQRCodeViewer\MunicipioAuth\Visma;

use Municipio\KulturkortetQRCodeViewer\MunicipioAuth\views\MunicipioAuthViewFactoryInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class VismaAuthControllerTest extends TestCase
{
    #[TestDox('renders anonymous view when no session and remote API login returns redirect URL')]
    public function testRenderWhenNoSessionAndRemoteApiLoginReturnsRedirectUrl(): void
    {
        // Fake Visma config api
        $context = $this->createMock(VismaContextInterface::class);
        $context->method('getHomeUrl')->willReturn('http://home.url');
        // Fake Visma API
        $api = $this->createMock(VismaApi::class);
        $api->method('shouldRemoteGetApiSession')->willReturn(false);
        $api->method('remoteApiLogin')->willReturn('http://redirect.url');

        // Fake viewfactory
        $viewFactory = $this->createMock(MunicipioAuthViewFactoryInterface::class);
        $viewFactory->expects($this->once())->method('whenAnonymous')->with('http://redirect.url')->willReturn('anonymous view');

        $controller = new VismaAuthController($context, $api);
        $result = $controller->render($viewFactory);
        static::assertSame('anonymous view', $result);
    }

    #[TestDox('renders authenticated view when can get session and remote API get session returns session')]
    public function testRenderWhenCanGetSessionAndRemoteApiGetSessionReturnsSession(): void
    {
        // Our expected user to be passed to view
        $expedtedUser = new VismaAuthorizedUser(['username' => '197001011234']);

        // Fake Visma context
        $context = $this->createMock(VismaContextInterface::class);
        // Fake API
        $api = $this->createMock(VismaApi::class);
        $context->method('getHomeUrl')->willReturn('http://home.url');
        $api->method('shouldRemoteGetApiSession')->willReturn(true);
        $api->method('remoteApiGetSession')->willReturn(['username' => '197001011234']);

        // Ensure our expected user is returned
        $userFactory = $this->createMock(VismaAuthorizedUserFactoryInterface::class);
        $userFactory->method('createAuthorizedUser')->with(['username' => '197001011234'])->willReturn($expedtedUser);

        // We expect the authenticated view to be rendered with the expected user
        $viewFactory = $this->createMock(MunicipioAuthViewFactoryInterface::class);
        $viewFactory->expects($this->once())->method('whenAuthenticated')->with($expedtedUser)->willReturn('authenticated view');

        $controller = new VismaAuthController($context, $api);
        $result = $controller->render($viewFactory);
        static::assertSame('authenticated view', $result);
    }

    #[TestDox('renders error view when no session and remote API login throws exception')]
    public function testRenderWhenNoSessionAndRemoteApiLoginThrowsException(): void
    {
        // Fake Visma context
        $context = $this->createMock(VismaContextInterface::class);
        $context->method('getHomeUrl')->willReturn('http://home.url');
        // Fake API
        $api = $this->createMock(VismaApi::class);
        $api->method('shouldRemoteGetApiSession')->willReturn(false);
        $api->method('remoteApiLogin')->willThrowException(new \Exception('Login error'));

        // We expect the error view to be rendered with the exception message and home URL
        $viewFactory = $this->createMock(MunicipioAuthViewFactoryInterface::class);
        $viewFactory->expects($this->once())->method('whenError')->with('Login error', 'http://home.url')->willReturn('error view');

        $controller = new VismaAuthController($context, $api);
        $result = $controller->render($viewFactory);
        static::assertSame('error view', $result);
    }
}
