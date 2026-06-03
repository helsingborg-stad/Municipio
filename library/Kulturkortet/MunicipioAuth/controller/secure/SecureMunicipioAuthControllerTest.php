<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\MunicipioAuth\controller\secure;

use Municipio\Kulturkortet\MunicipioAuth\controller\MunicipioAuthControllerInterface;
use Municipio\Kulturkortet\MunicipioAuth\navigation\MunicipioAuthNavigationInterface;
use Municipio\Kulturkortet\MunicipioAuth\user\MunicipioAuthenticatedUserInterface;
use Municipio\Kulturkortet\MunicipioAuth\views\MunicipioAuthViewFactoryInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class SecureMunicipioAuthControllerTest extends TestCase
{
    #[TestDox('should delegate validateUser to inner controller')]
    public function testValidateUser(): void
    {
        // validateUser is a basic check to prevent outdated tokens, logic errors
        // or other to propagate
        $user = $this->createMock(MunicipioAuthenticatedUserInterface::class);

        $inner = $this->createMock(MunicipioAuthControllerInterface::class);
        $inner->expects(static::once())->method('validateUser')->with($user)->willReturn($user);

        $config = $this->createMock(SecureMunicipioAuthConfigInterface::class);

        $controller = new SecureMunicipioAuthController($inner, $config);

        static::assertSame($user, $controller->validateUser($user));
    }

    #[TestDox('should delegate tryLogoutUser to inner controller and try to clear cookie')]
    public function testTryLogoutUser(): void
    {
        // tryLogoutUser is an imperative method designed to propagate in a decorator chain
        // it is typically called from a view when whenLogout is invoked
        $user = $this->createMock(MunicipioAuthenticatedUserInterface::class);

        $inner = $this->createMock(MunicipioAuthControllerInterface::class);
        $inner->expects(static::once())->method('tryLogoutUser')->with($user);

        $config = $this->createMock(SecureMunicipioAuthConfigInterface::class);
        $cookieStrategy = $this->createMock(CookieStrategyInterface::class);
        $cookieStrategy->expects(static::once())->method('setCookie')->with(null);

        $controller = new SecureMunicipioAuthController($inner, $config, $cookieStrategy);

        $controller->tryLogoutUser($user);
    }

    #[TestDox('should render inner controller view when no user is authenticated')]
    public function testRenderAnonymous(): void
    {
        // we are called with no intended action
        $navigation = $this->createMock(MunicipioAuthNavigationInterface::class);
        $navigation->expects(static::once())->method('getQueryParameter')->with('action')->willReturn(null);

        $config = $this->createMock(SecureMunicipioAuthConfigInterface::class);

        // we have no user and certainly no valid user
        $inner = $this->createMock(MunicipioAuthControllerInterface::class);
        $inner->expects(static::once())->method('validateUser')->with(null)->willReturn(null);
        $inner->expects(static::once())->method('render')->with(static::anything(), $navigation)->willReturn('inner controller rendering');

        $viewFactory = $this->createMock(MunicipioAuthViewFactoryInterface::class);

        // rig the case that there is no cookie
        $cookieStrategy = $this->createMock(CookieStrategyInterface::class);
        $cookieStrategy->expects(static::once())->method('getCookie')->with($config)->willReturn(null);
        $controller = new SecureMunicipioAuthController($inner, $config, $cookieStrategy);

        // we just want to make sure this doesn't throw an error when no user is authenticated, the inner controller should handle this case and render the appropriate view
        $result = $controller->render($viewFactory, $navigation);
        static::assertSame('inner controller rendering', $result);
    }

    #[TestDox('should render logout view for a valid authenticated user')]
    public function testRenderLogout(): void
    {
        $navigation = $this->createMock(MunicipioAuthNavigationInterface::class);
        $navigation->expects(static::once())->method('getQueryParameter')->with('action')->willReturn('logout');

        $config = $this->createMock(SecureMunicipioAuthConfigInterface::class);

        $inner = $this->createMock(MunicipioAuthControllerInterface::class);
        $inner
            ->expects(static::exactly(2))
            ->method('validateUser')
            ->with(static::isInstanceOf(MunicipioAuthenticatedUserInterface::class))
            ->willReturnCallback(static fn($user) => $user);
        $inner->expects(static::once())->method('tryLogoutUser')->with(static::isInstanceOf(MunicipioAuthenticatedUserInterface::class));
        $inner->expects(static::once())->method('getLoginUrl')->with($navigation)->willReturn('http://login.url');

        $viewFactory = $this->createMock(MunicipioAuthViewFactoryInterface::class);
        $viewFactory->expects(static::once())->method('whenLogOut')->with(static::isInstanceOf(MunicipioAuthenticatedUserInterface::class), $navigation, 'http://login.url')->willReturn('logged out view');

        $cookieStrategy = $this->createMock(CookieStrategyInterface::class);
        $cookieStrategy->expects(static::once())->method('getCookie')->with($config)->willReturn('cookie value');
        $cookieStrategy->expects(static::once())->method('setCookie')->with('', $config);

        $jwtStrategy = $this->createMock(JWTStrategyInterface::class);
        $jwtStrategy
            ->expects(static::once())
            ->method('tryDecode')
            ->with('cookie value', $config)
            ->willReturn([
                'x-sid' => '00000000-0000-0000-0000-000000000000',
                'sub' => '19700101-0000',
                'x-cn' => 'Test Person',
                'x-gn' => 'Person',
                'x-sn' => 'Test',
            ]);

        $controller = new SecureMunicipioAuthController($inner, $config, $cookieStrategy, $jwtStrategy);

        // we just want to make sure this doesn't throw an error when no user is authenticated, the inner controller should handle this case and render the appropriate view
        $result = $controller->render($viewFactory, $navigation);
        static::assertSame('logged out view', $result);
    }

    #[TestDox('should render error view when cookie retrieval fails')]
    public function testRenderReturnsErrorWhenCookieRetrievalFails(): void
    {
        $navigation = $this->createMock(MunicipioAuthNavigationInterface::class);

        $config = $this->createMock(SecureMunicipioAuthConfigInterface::class);

        $inner = $this->createMock(MunicipioAuthControllerInterface::class);
        $inner->expects(static::never())->method('render');
        $inner->expects(static::once())->method('getLoginUrl')->with($navigation)->willReturn('http://login.url');

        $viewFactory = $this->createMock(MunicipioAuthViewFactoryInterface::class);
        $viewFactory->expects(static::once())->method('whenError')->with('cookie exception', $navigation, 'http://login.url')->willReturn('error view');

        $cookieStrategy = $this->createMock(CookieStrategyInterface::class);
        $cookieStrategy
            ->expects(static::once())
            ->method('getCookie')
            ->with($config)
            ->willThrowException(new \RuntimeException('cookie exception'));

        $controller = new SecureMunicipioAuthController($inner, $config, $cookieStrategy);

        $result = $controller->render($viewFactory, $navigation);

        static::assertSame('error view', $result);
    }

    #[TestDox('should render error view when inner controller render fails')]
    public function testRenderReturnsErrorWhenInnerRenderFails(): void
    {
        $navigation = $this->createMock(MunicipioAuthNavigationInterface::class);
        $navigation->expects(static::once())->method('getQueryParameter')->with('action')->willReturn(null);

        $config = $this->createMock(SecureMunicipioAuthConfigInterface::class);

        $inner = $this->createMock(MunicipioAuthControllerInterface::class);
        $inner->expects(static::once())->method('validateUser')->with(null)->willReturn(null);
        $inner->expects(static::once())->method('getLoginUrl')->with($navigation)->willReturn('http://login.url');
        $inner
            ->expects(static::once())
            ->method('render')
            ->with(static::anything(), $navigation)
            ->willThrowException(new \RuntimeException('inner render failed'));

        $viewFactory = $this->createMock(MunicipioAuthViewFactoryInterface::class);
        $viewFactory->expects(static::once())->method('whenError')->with('inner render failed', $navigation, 'http://login.url')->willReturn('error view');

        $cookieStrategy = $this->createMock(CookieStrategyInterface::class);
        $cookieStrategy->expects(static::once())->method('getCookie')->with($config)->willReturn(null);

        $controller = new SecureMunicipioAuthController($inner, $config, $cookieStrategy);

        $result = $controller->render($viewFactory, $navigation);

        static::assertSame('error view', $result);
    }
}
