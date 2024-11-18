<?php

namespace PerSeo\Middleware\Language\Test;

use PerSeo\Middleware\Language\Language;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

class LanguageTest extends TestCase
{
    public function testLanguageFromCookie(): void
    {
        // Arrange
        $container = $this->createMock(ContainerInterface::class);
        $settings = [
            'languages' => ['en', 'it'],
            'language' => 'en',
        ];
        $container->method('has')->with('settings_global')->willReturn(true);
        $container->method('get')->with('settings_global')->willReturn($settings);

        $middleware = new Language($container);

        $request = $this->createMock(ServerRequestInterface::class);
        $handler = $this->createMock(RequestHandlerInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $request->method('getCookieParams')->willReturn(['lang' => 'it']);
        $request->method('getServerParams')->willReturn([]);
        $request->expects($this->once())
            ->method('withAttribute')
            ->with('language', 'it')
            ->willReturnSelf();

        $handler->method('handle')->with($request)->willReturn($response);

        // Act
        $result = $middleware->process($request, $handler);

        // Assert
        $this->assertSame($response, $result);
    }

    public function testLanguageFromServer(): void
    {
        // Arrange
        $container = $this->createMock(ContainerInterface::class);
        $settings = [
            'languages' => ['en', 'it'],
            'language' => 'en',
        ];
        $container->method('has')->with('settings_global')->willReturn(true);
        $container->method('get')->with('settings_global')->willReturn($settings);

        $middleware = new Language($container);

        $request = $this->createMock(ServerRequestInterface::class);
        $handler = $this->createMock(RequestHandlerInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $request->method('getCookieParams')->willReturn([]);
        $request->method('getServerParams')->willReturn(['HTTP_ACCEPT_LANGUAGE' => 'it-IT']);
        $request->expects($this->once())
            ->method('withAttribute')
            ->with('language', 'it')
            ->willReturnSelf();

        $handler->method('handle')->with($request)->willReturn($response);

        // Act
        $result = $middleware->process($request, $handler);

        // Assert
        $this->assertSame($response, $result);
    }

    public function testLanguageFromServerNotPresent(): void
    {
        // Arrange
        $container = $this->createMock(ContainerInterface::class);
        $settings = [
            'languages' => ['en', 'it'],
            'language' => 'en',
        ];
        $container->method('has')->with('settings_global')->willReturn(true);
        $container->method('get')->with('settings_global')->willReturn($settings);

        $middleware = new Language($container);

        $request = $this->createMock(ServerRequestInterface::class);
        $handler = $this->createMock(RequestHandlerInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $request->method('getCookieParams')->willReturn([]);
        $request->method('getServerParams')->willReturn(['HTTP_ACCEPT_LANGUAGE' => 'fr-FR']);
        $request->expects($this->once())
            ->method('withAttribute')
            ->with('language', 'en')
            ->willReturnSelf();

        $handler->method('handle')->with($request)->willReturn($response);

        // Act
        $result = $middleware->process($request, $handler);

        // Assert
        $this->assertSame($response, $result);
    }

    public function testDefaultLanguage(): void
    {
        // Arrange
        $container = $this->createMock(ContainerInterface::class);
        $settings = [
            'languages' => ['en', 'it'],
            'language' => 'en',
        ];
        $container->method('has')->with('settings_global')->willReturn(true);
        $container->method('get')->with('settings_global')->willReturn($settings);

        $middleware = new Language($container);

        $request = $this->createMock(ServerRequestInterface::class);
        $handler = $this->createMock(RequestHandlerInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $request->method('getCookieParams')->willReturn([]);
        $request->method('getServerParams')->willReturn([]);
        $request->expects($this->once())
            ->method('withAttribute')
            ->with('language', 'en')
            ->willReturnSelf();

        $handler->method('handle')->with($request)->willReturn($response);

        // Act
        $result = $middleware->process($request, $handler);

        // Assert
        $this->assertSame($response, $result);
    }
}
