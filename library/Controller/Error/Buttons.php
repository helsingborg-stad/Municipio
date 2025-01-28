<?php

namespace Municipio\Controller\Error;

use Municipio\Helper\WpService;

class Buttons
{
    public static function getHomeButton(array $args = []): array
    {
        return array_merge($args, [
            'label' => __("Go to homepage", 'municipio'),
            'href'  => '/',
            'icon'  => 'home',
            'color' => 'secondary',
            'style' => 'filled'
        ]);
    }

    public static function getReturnButton(array $args = []): array
    {
        return array_merge($args, [
            'label' => __("Go back", 'municipio'),
            'href'  => 'javascript:history.go(-1);',
            'icon'  => 'arrow_back',
            'color' => 'secondary',
            'style' => 'filled'
        ]);
    }

    public static function getLoginButton(array $args = [], ?string $redirectTo = null): array
    {
        $wpService = WpService::get();

        return array_merge($args, [
            'label' => __("Log in", 'municipio'),
            'href'  => $wpService->wpLoginUrl($redirectTo ?? ''),
            'icon'  => 'login',
            'color' => 'primary',
            'style' => 'filled'
        ]);
    }
}
