<?php

namespace Municipio\Navigation;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddFilter;
use WpService\Contracts\GetThemeMod;

class Drawer implements Hookable
{
    public function __construct(private AddFilter&GetThemeMod $wpService)
    {
    }

    public function addHooks(): void
    {
        $this->wpService->addFilter('Municipio/Navigation/Items', array($this, 'addDrawerButtonToPrimaryMenu'), 10, 2);
    }

    public function addDrawerButtonToPrimaryMenu($data, $identifier)
    {
        if (
            $identifier !== 'primary' ||
            $this->wpService->getThemeMod('header_apperance') !== 'business' ||
            $this->wpService->getThemeMod('header_drawer_location') !== 'bottom'
        ) {
            return $data;
        }

        $wantedScreenSizes = $this->wpService->getThemeMod('drawer_screen_sizes') ?? [];
        $screenSizes       = ['xs', 'sm', 'md', 'lg', 'xl'];

        $screenSizes = array_map(function ($size) use ($wantedScreenSizes) {
            if (!in_array($size, $wantedScreenSizes)) {
                return 'u-display--none@' . $size;
            }
            return '';
        }, $screenSizes);

        $data[] = [
            'style'         => 'button',
            'buttonStyle'   => $this->wpService->getThemeMod('header_trigger_button_type') ?? 'basic',
            'buttonColor'   => $this->wpService->getThemeMod('header_trigger_button_color') ?? 'default',
            'post_parent'   => null,
            'post_type'     => null,
            'active'        => false,
            'ancestor'      => false,
            'children'      => false,
            'label'         => __('Menu', 'municipio'),
            'href'          => null,
            'icon'          => [
            'icon'          => 'menu',
            'size'          => 'md',
            'classList'     => ['c-nav__icon'],
            'attributeList' => [
                'aria-label' => __('Menu', 'municipio'),
                ]
            ],
            'attributeList' => [
                'aria-label'          => __('Menu', 'municipio'),
                'data-simulate-click' => '#mobile-menu-trigger-open'
            ],
            'classList'     => $screenSizes
        ];

        return $data;
    }
}
