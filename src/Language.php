<?php

namespace PerSeo\Middleware\Language;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\MiddlewareInterface;

class Language implements MiddlewareInterface
{
    /**
     * @var array{languages: array<string>, language: string}
     */
    protected array $settings;

    public function __construct(ContainerInterface $container)
    {
        /**@var array{languages: array<string>, language: string} */
        $this->settings = (array) ($container->has('settings_global') ? $container->get('settings_global') : ['languages' => ['en'], 'language' => 'en']);
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        $cookie = (array) $request->getCookieParams();
        $server = (array) $request->getServerParams();
        $httplang = (string) array_key_exists('HTTP_ACCEPT_LANGUAGE', $server) ? strtolower(substr($server['HTTP_ACCEPT_LANGUAGE'], 0, 2)) : ($this->settings['language'] ?? 'en');
        $languages = $this->settings['languages'] ?? [];
        if (isset($cookie['lang']) && in_array(strtolower($cookie['lang']), $languages)) {
            $currlang = strtolower($cookie['lang']);
        } else {
            if (in_array($httplang, $languages)) {
                $currlang = $httplang;
            } else {
                $currlang = $this->settings['language'] ?? 'en';
            }
        }
        $request = $request->withAttribute('language', $currlang);
        $response = $handler->handle($request);
        return $response;
    }
}