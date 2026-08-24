<?php

declare(strict_types=1);

namespace Municipio\Search\Index\Admin;

use AcfService\Contracts\AddOptionsSubPage;
use \Municipio\Search\Index\Helper\Index as Instance;
use \Municipio\Search\Index\Helper\Options as Options;
use Municipio\Search\Index\Provider\ProviderFactory;
use WpService\Contracts\AddAction;
use WpService\Contracts\AddFilter;
use WpService\Contracts\DoAction;
use WpService\Contracts\GetOption;
use WpService\Contracts\UpdateOption;

class Settings
{
    private const OPTIONS_PAGE_SLUG = 'algolia-index-settings';
    public const ACF_TO_LEGACY_OPTIONS_MAP = [
        'algolia_index_application_id' => 'application_id',
        'algolia_index_api_key' => 'api_key',
        'algolia_index_public_api_key' => 'public_api_key',
        'algolia_index_index_name' => 'index_name',
    ];

    public function __construct(
        private AddAction&AddFilter&GetOption&UpdateOption&DoAction $wpService, 
        private AddOptionsSubPage $acfService)
    {
        $this->wpService->addAction('acf/init', [$this, 'registerOptionsPage']);
        $this->wpService->addAction('acf/save_post', [$this, 'pushSettingsOnSave'], 20);
        $this->wpService->addFilter('acf/load_field', [$this, 'addProvidersAsOptions'], 10, 1);

        // Migrate legacy options to ACF fields
        $this->wpService->addFilter('acf/load_value', [$this, 'loadLegacyOptionValues'], 10, 3);
        $this->wpService->addFilter('acf/update_value', [$this, 'clearLegacyOptionsOnSave'], 10, 4);

        // Trigger settings send for algolia provider
        $this->wpService->addAction('AlgoliaIndex/SendSettings', [$this, 'sendAlgoliaSettings']);
    }

    public function addProvidersAsOptions($field)
    {
        if ($field['name'] === 'algolia_index_search_provider') {
            $providers = \array_keys(ProviderFactory::getProviders());
            foreach ($providers as $provider) {
                $field['choices'][$provider] = ucfirst($provider);
            }
        }
        return $field;
    }

    public function registerOptionsPage()
    {
        if (function_exists('acf_add_options_sub_page')) {
            $this->acfService->addOptionsSubPage([
                'page_title' => __('Algolia Index', 'municipio'),
                'menu_title' => __('Algolia Index', 'municipio'),
                'menu_slug' => Settings::OPTIONS_PAGE_SLUG,
                'capability' => 'manage_options',
                'parent_slug' => 'options-general.php',
                'autoload' => true,
            ]);
        }
    }

    public function loadLegacyOptionValues($value, $post_id, $field)
    {
        if (array_key_exists($field['name'], Settings::ACF_TO_LEGACY_OPTIONS_MAP)) {
            return !empty($value)
                ? $value
                : $this->wpService->getOption('algolia_index')[Settings::ACF_TO_LEGACY_OPTIONS_MAP[$field['name']]] ?? '';
        }
        return $value;
    }

    public function clearLegacyOptionsOnSave($value, $post_id, $field, $original)
    {
        if (array_key_exists($field['name'], Settings::ACF_TO_LEGACY_OPTIONS_MAP)) {
            $legacyOptions = $this->wpService->getOption('algolia_index', []);
            $legacyKey = Settings::ACF_TO_LEGACY_OPTIONS_MAP[$field['name']];
            if (isset($legacyOptions[$legacyKey])) {
                unset($legacyOptions[$legacyKey]);
                $this->wpService->updateOption('algolia_index', $legacyOptions);
            }
        }

        return $value;
    }

    public function pushSettingsOnSave($post_id)
    {
        if ($post_id !== 'options' || empty($_GET['page']) || ($_GET['page'] ?? '') !== Settings::OPTIONS_PAGE_SLUG) {
            return;
        }

        $this->wpService->doAction('AlgoliaIndex/SendSettings');
    }

    /**
     * Send searchable attributes.
     *
     * @return void
     */
    public function sendAlgoliaSettings()
    {
        if (!Options::isConfigured()) {
            return;
        }

        Instance::getIndex()->setSettings();
    }

    /**
     * Display summary
     *
     * @return void
     */
    public function algoliaSettingsSummaryCallback()
    {
        echo '<p>The following data is used by the algoia integration.</p>';
        echo '<table>';
        echo '
          <tr><td style="min-width: 100px;">
            <strong>Application ID: </strong>
          </td><td>' . Options::applicationId() . '</td></tr>';
        echo '<tr><td><strong>API Key: </strong></td><td>' . Options::apiKey() . '</td></tr>';
        echo '<tr><td><strong>Public API Key: </strong></td><td>' . Options::PublicApiKey() . '</td></tr>';
        echo '<tr><td><strong>Index Name: </strong></td><td>' . Options::indexName() . '</td></tr>';
        echo '</table>';
    }
}
