<?php

namespace Modularity\Module\Markdown;

use WP_Error;
use Modularity\Module\Markdown\Providers\ProviderInterface;
use League\CommonMark\CommonMarkConverter;
class Markdown extends \Modularity\Module {
    public $slug = 'markdown';
    public $supports = array();
    public $isBlockCompatible = true;
    public $cacheTtl = (MINUTE_IN_SECONDS * 60 * 12); //Seconds (12 hours)
    private $providers = [];
    private $lastUpdatedKey = '_last_updated';
    private $nextUpdateKey = '_next';

    public function init()
    {
        //Setup module
        $this->nameSingular = __("Markdown", 'modularity');
        $this->namePlural = __("Markdown", 'modularity');
        $this->description = __("Outputs a markdown resource as module.", 'modularity');

        //Setup providers
        $this->providers = [
            new Providers\Github(),
            new Providers\Bitbucket(),
            new Providers\Gitlab(),
            new Providers\AzureDevOps(),
            new Providers\CGit(),
            new Providers\Gitea(),
            new Providers\Codeberg(),
            new Providers\SourceHut(),
        ];

        //Filter example fields
        add_filter('acf/prepare_field/key=field_67506eebcdbfd', array($this, 'createDocumentationField'));

        //Delete transients when saving
        add_action('acf/save_post', array($this, 'deleteTransients'), 10, 1);
    }

    /**
     * Delete transients when saving.
     * 
     * @return void
     */
    public function deleteTransients($postId): void
    {
        if(get_post_type($postId) !== 'mod-markdown') {
            return;
        }

        $fields = $this->getFields();
        $markdownUrl = $fields['mod_markdown_url'] ?? false;

        if ($markdownUrl) {
            $transientKey = $this->createTransientKey($markdownUrl);
            delete_transient($transientKey);
            delete_transient($transientKey . $this->lastUpdatedKey);
            delete_transient($transientKey . $this->nextUpdateKey);
        }
    }

    /**
     * Create custom documentation field.
     * 
     * @param array $field The field array.
     * 
     * @return array The field array.
     */
    public function createDocumentationField(array $field): array 
    {

        //Get language
        $language = $this->getLanguage();

        // Initialize table content
        $tableContent = '<table class="widefat striped">';
        $tableContent .= '<thead>';
        $tableContent .= '<tr>';
        $tableContent .= '<th>' . $language->tableProviderHead . '</th>';
        $tableContent .= '<th> ' . $language->tableExampleHead .' </th>';
        $tableContent .= '</tr>';
        $tableContent .= '</thead>';
        $tableContent .= '<tbody>';
    
        // Loop through providers
        foreach ($this->providers as $provider) {
            if ($provider instanceof \Modularity\Module\Markdown\Providers\ProviderInterface) {
                $name = esc_html($provider->getName());
                $example = esc_html($provider->getExample());
    
                $tableContent .= '<tr>';
                $tableContent .= "<td>{$name}</td>";
                $tableContent .= "<td>{$example}</td>";
                $tableContent .= '</tr>';
            }
        }
    
        $tableContent .= '</tbody>';
        $tableContent .= '</table>';
    
        $field['message'] = $tableContent;
    
        return $field;
    }

    /**
     * Get the module data.
     * 
     * @return array The module data.
     */
    public function data(): array
    {
        //Get fields
        $fields = $this->getFields();

        //Handle data
        $markdownUrl    = $fields['mod_markdown_url'] ?: false;
        $isMarkdownUrl = $this->checkIfIsValidMarkdownProvider($markdownUrl, ...$this->providers);
        $markdownContent = $isMarkdownUrl ? $this->getDocument($markdownUrl) : false;
        $isWrapped  = $fields['mod_markdown_wrap_in_container'] ?? false;
        $markDownImplementation = $isMarkdownUrl ? $this->getMarkdownProvider($markdownUrl, ...$this->providers) : false;

        if(!is_wp_error($markdownContent)) {
            $parsedMarkdown = $isMarkdownUrl ? $this->parseMarkdown(
                $this->filterMarkDownContent($markdownContent, $fields ?? []),
                $markDownImplementation
            ) : false;
            $wpError = (is_wp_error($parsedMarkdown)) ? $parsedMarkdown : false;
        } else {
            $wpError = $markdownContent;
        }

        if(is_wp_error($wpError)) {
            $parsedMarkdown = false;
        }
       
        $showMarkdownSource = $fields['mod_markdown_show_source'] ?: false;

        //Return data
        return [
            'isMarkdownUrl' => $isMarkdownUrl,
            'wpError' => $wpError,
            'markdownContent' => $markdownContent,
            'parsedMarkdown' => $parsedMarkdown,
            'showMarkdownSource' => $showMarkdownSource,
            'markdownUrl' => $markdownUrl,
            'markdownLastUpdated' => get_transient($this->createTransientKey($markdownUrl) . $this->lastUpdatedKey),
            'markdownNextUpdate' => get_transient($this->createTransientKey($markdownUrl) . $this->nextUpdateKey),
            'language' => $this->getLanguage(),
            'isWrapped' => $isWrapped,
        ];
    }

    /**
     * Get the language object.
     * 
     * @return object The language object.
     */
    private function getLanguage(): object 
    {
        return (object) [
            'sourceUrl' =>  __('Source Url', 'modularity'),
            'nextUpdate' => __('Next update', 'modularity'),
            'lastUpdated' => __('Last updated', 'modularity'),
            'fetchError' => __('We could not fetch any content at this moment. Please try again later.', 'modularity'),
            'parseError' => __('The url provided could not be parsed by any of the allowed providers.', 'modularity'),
            'tableProviderHead' => __('Provider', 'modularity'),
            'tableExampleHead' => __('Example', 'modularity'),
        ];
    }

    /**
     * Filter markdown content.
     * 
     * @param string $markdownContent The markdown content.
     * 
     * @return string The filtered markdown content.
     */
    private function filterMarkDownContent(string $markdownContent, array $fields): string
    {
        $filters = [
            new Filters\DemoteTitles($fields),
            new Filters\RelativeAssets($fields),
        ]; 

        foreach ($filters as $filter) {
            $markdownContent = $filter->filter($markdownContent);
        }

        return $markdownContent;
    }

    /**
     * Check if the url is a valid markdown url.
     */
    private function checkIfIsValidMarkdownProvider($url, ProviderInterface ...$providers): bool
    {
        if(!is_null($this->getMarkdownProvider($url, ...$providers))) {
            return true;
        }
        return false;
    }

    /**
     * Get markdown provider.
     */
    private function getMarkdownProvider($url, ProviderInterface ...$providers): ProviderInterface | null
    {
        foreach ($providers as $provider) {
            if ($provider->isValidProviderUrl($url)) {
                return $provider;
            }
        }
        return null;
    }

    /**
     * Parse markdown content.
     */
    private function parseMarkdown(string $markdown, ProviderInterface $markDownImplementation): string | \WP_Error
    {
        try {
            if($markDownImplementation) {
                $converter = $markDownImplementation->implementation();
            } else {
                $converter = new CommonMarkConverter();
            }
            return $converter->convert($markdown)->getContent();
        } catch (\Exception $e) {
            return new \WP_Error('parse_error', __('The url provided could not be parsed as markdown.', 'modularity'));
        }
    }

    /**
     * Get document from remote URL.
     * 
     * @param string $requestUrl The URL to request.
     * 
     * @return mixed The remote document.
     */
    public function getDocument(string $requestUrl): string | \WP_Error
    {
        $requestArgs = [
            'headers' => [
                'Content-Type: application/json',
            ]
        ];

        return $this->maybeRetrieveCachedResponse($requestUrl, $requestArgs, true);
    }

    /**
     * Retrieve cached response if available or get remote response and set cached response.
     *
     * @param string $requestUrl The URL to request.
     * @param array $requestArgs Optional. Arguments for the remote request.
     * @param bool $cache Whether to use cached response or not.
     * 
     * @return mixed Cached response if available or remote response.
     */
    private function maybeRetrieveCachedResponse(string $requestUrl, array $requestArgs, bool $cache) : string | \WP_Error
    {
        $transientKey = $this->createTransientKey($requestUrl);

        if ($cache && $cachedDocument = get_transient($transientKey)) {
            return $cachedDocument;
        }

        return $this->getRemoteAndSetCachedResponse($requestUrl, $transientKey, $requestArgs);
    }

    /**
     * Get remote response and set cached response.
     *
     * @param string $requestUrl The URL to request.
     * @param string $transientKey The transient key for caching the response.
     * @param array $requestArgs Optional. Arguments for the remote request.
     * 
     * @return string|WP_Error Remote response.
     */
    private function getRemoteAndSetCachedResponse(string $requestUrl, string $transientKey, array $requestArgs = []) : string | \WP_Error
    {
        $response = wp_remote_get($requestUrl, $requestArgs); 

        if (is_wp_error($response) || ($responseCode = wp_remote_retrieve_response_code($response) !== 200)) {    
            if($responseCode !== 200) {
                return new \WP_Error('fetch_error', __('We could not fetch any content at this moment. Please try again later. Response Code ' . ($responseCode), 'modularity'));
            }
            return new \WP_Error('fetch_error', __('We could not fetch any content at this moment. Please try again later.', 'modularity'));
        }

        if ($data = wp_remote_retrieve_body($response)) {
            set_transient($transientKey, $data, $this->cacheTtl);
            set_transient($transientKey . $this->lastUpdatedKey, date("Y-m-d H:i", time()), $this->cacheTtl);
            set_transient($transientKey . $this->nextUpdateKey, date("Y-m-d H:i", time() + $this->cacheTtl), $this->cacheTtl);
        }

        return $data;
    }

    /**
     * Create a transient key for caching the response.
     *
     * @param string $requestUrl The URL to request.
     * 
     * @return string The transient key for caching the response.
     */
    private function createTransientKey($requestUrl) : string
    {
        return "mod_markdown_" . md5(serialize($requestUrl));
    }
}