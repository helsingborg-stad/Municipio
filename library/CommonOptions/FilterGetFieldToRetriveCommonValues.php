<?php

namespace Municipio\CommonOptions;

use Municipio\Helper\SiteSwitcher\SiteSwitcher;
use WpService\WpService;
use Municipio\HooksRegistrar\Hookable;

class FilterGetFieldToRetriveCommonValues implements Hookable
{
    //TODO: Populate this with a options page
    protected array $optionsToFilter = [
        'some_option',
        'another_option',
    ];

    public function __construct(private WpService $wpService , private SiteSwitcher $siteSwitcher){}

    public function addHooks(): void
    {
      $this->wpService->addFilter('pre_option', [$this, 'filterOption'], 10, 3);
    }

    /**
     * Filters the option value based on predefined conditions.
     *
     * @param mixed  $preOption The default value to return instead of the option value.
     * @param string $option    The name of the option being fetched.
     * @param mixed  $default   The default value if the option does not exist.
     * @return mixed The filtered value for the option.
     */
    public function filterOption($preOption, string $option, $default)
    {
        static $isFiltering = false;
        if ($isFiltering) {
          return $preOption;
        }

        try {
            $isFiltering = true;
            if (in_array($option, $this->optionsToFilter, true)) {
              return $this->getOptionFromMainBlog($option, $default);
            }
        } finally {
            $isFiltering = false; // Always reset, even on exceptions.
        }

        return $preOption;
    }

    /**
     * Fetches an option from the main blog using the SiteSwitcher.
     *
     * @param string $option  The name of the option.
     * @param mixed  $default The default value if the option does not exist.
     * @return mixed The option value.
     */
    protected function getOptionFromMainBlog(string $option, $default)
    {
        return $this->siteSwitcher->runInSite(
            $this->wpService->getMainSiteId(),
            function () use ($option, $default) {
              return $this->wpService->getOption($option, $default);
            }
        );
    }
}