<?php

namespace Municipio\Blocks\Footer;

use Municipio\Helper\CurrentPostId;
use Municipio\Helper\FormatObject;
use Municipio\Helper\SiteSwitcher\SiteSwitcherInterface;
use WpService\WpService;

/**
 * This class serves as the base controller for all controllers in the theme.
 */
class Data
{
    /**
     * Holds the view's data
     * @var array
     */
    public $data = [];

    /**
     * Init data fetching
     * @var object
     */
    public function __construct(
        private WpService $wpService,
        private SiteSwitcherInterface $siteSwitcher,
    ) {
        $this->data['homeUrl'] = $this->getHomeUrl();

        $this->data['pageID'] = CurrentPostId::get();
        $this->data['pageParentID'] = $this->getPageParentID();

        //Customization data
        $this->data['customizer'] = apply_filters('Municipio/Controller/Customizer', []);

        //Logotypes
        $this->data['logotype'] = $this->getLogotype($this->data['customizer']->headerLogotype ?? 'standard', true);
        $this->data['footerLogotype'] = $this->getLogotype($this->data['customizer']->footerLogotype ?? 'negative');
        $this->data['subfooterLogotype'] = $this->getSubfooterLogotype($this->data['customizer']->footerSubfooterLogotype ?? false);
        $this->data['emblem'] = $this->getEmblem();

        // Footer
        [$footerStyle, $footerColumns, $footerAreas] = $this->getFooterSettings();
        $this->data['footerColumns'] = $footerColumns;
        $this->data['footerGridSize'] = $footerStyle === 'columns' ? floor(12 / $footerColumns) : 12;
        $this->data['footerAreas'] = $footerAreas;
        $this->data['footerTextAlignment'] = $this->data['customizer']->municipioCustomizerSectionComponentFooterMain['footerTextAlignment'] ?? 'left';
    }

    /**
     * Get the emblem to use
     *
     * @param array $data
     * @return array
     */
    public function componentDataEmblemFilter($data)
    {
        $contexts = isset($data['context']) ? (array) $data['context'] : [];
        if (in_array('component.image.placeholder.icon', $contexts)) {
            $data['label'] = __('Emblem', 'municipio');
            $data['icon'] = $this->getEmblem();
        }
        return $data;
    }

    /**
     * Get current parent ID
     *
     * @return integer
     */
    private function getPageParentID(): int
    {
        $parentId = wp_get_post_parent_id(CurrentPostId::get());
        return is_int($parentId) ? $parentId : 0;
    }

    /**
     * Retrieves the footer settings.
     *
     * @return array An array containing the footer style, number of footer columns, and footer areas.
     */
    private function getFooterSettings()
    {
        $footerStyle = $this->data['customizer']->municipioCustomizerSectionComponentFooterMain['footerStyle'] ?? 'standard';
        $footerAreas = ['footer-area'];
        $footerColumns = 1;
        if ($footerStyle === 'columns') {
            $footerColumns = $this->data['customizer']->municipioCustomizerSectionComponentFooterMain['footerColumns'] ?? 1;
            for ($i = 1; $i < $footerColumns; $i++) {
                $footerAreas[] = 'footer-area-column-' . $i;
            }
        }

        return [$footerStyle, $footerColumns, $footerAreas];
    }

    /**
     * Get the appropriate home URL, considering multisite and subdirectory setups.
     *
     * @return string
     */
    private function getHomeUrl(): string
    {
        if (is_multisite() && !is_subdomain_install() && !is_main_site()) {
            $homeUrl = $this->siteSwitcher->runInSite(
                $this->wpService->getMainSiteId(),
                function () {
                    return $this->getHomeUrl();
                },
            );
        } else {
            $homeUrl = get_home_url();
        }

        // Ensure $homeUrl is always a string
        if (!is_string($homeUrl) || $homeUrl === null) {
            $homeUrl = '';
        }

        $filtered = apply_filters('Municipio/homeUrl', esc_url($homeUrl));
        return is_string($filtered) ? $filtered : '';
    }

    /**
     * Get emblem svg
     *
     * @return bool|string
     */
    private function getEmblem()
    {
        if (empty($logotypeEmblem = $this->data['customizer']?->logotypeEmblem)) {
            return false;
        }

        return $logotypeEmblem;
    }

    /**
     * Get the logotype url.
     *
     * @param string $variant
     * @return string Logotype file url, defaults to the theme logo if not found.
     */
    private function getLogotype($variant = 'standard', $fallback = false): string
    {
        $variantKey = 'logotype';

        if ($variant !== 'standard' && !is_null($variant)) {
            $variantKey = FormatObject::camelCaseString("{$variantKey}_{$variant}");
        }

        $logotypeUrl = isset($this->data['customizer']->$variantKey) ? $this->data['customizer']->{$variantKey} : '';

        if (empty($logotypeUrl) && $fallback) {
            return $this->getDefaultLogotype();
        }

        return $logotypeUrl;
    }

    /**
     * Returns the default logotype.
     *
     * @return string The URL of the default logotype image.
     */
    private function getDefaultLogotype(): string
    {
        return get_stylesheet_directory_uri() . '/assets/images/municipio.svg';
    }

    /**
     * Get the subfooter logotype
     *
     * @param string $variant
     * @return string|boolean
     */
    private function getSubfooterLogotype($variant = 'standard')
    {
        if (!$variant) {
            return false;
        }

        if ($variant === 'custom') {
            return $this->data['customizer']->footerSubfooterCustomLogotype;
        }

        return $this->getLogotype($variant) ?? false;
    }

    /**
     * Returns the data
     * @return array Data
     */
    public function getData()
    {
        //Create filters for all data vars
        if (isset($this->data) && !empty($this->data) && is_array($this->data)) {
            foreach ($this->data as $key => $value) {
                $this->data[$key] = apply_filters('Municipio/' . $key, $value);
            }
        }

        //Old depricated filter
        $this->data = apply_filters_deprecated('HbgBlade/data', array($this->data), '2.0', 'Municipio/viewData');

        //General filter
        return $this->data;
    }
}
