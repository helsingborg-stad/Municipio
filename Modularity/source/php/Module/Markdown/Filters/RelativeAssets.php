<?php 

namespace Modularity\Module\Markdown\Filters;

use Modularity\Module\Markdown\Filters\FilterInterface;

class RelativeAssets implements FilterInterface
{
    private string $baseUrl;

    public function __construct(private array $fields)
    {   
        $this->baseUrl = $this->getBaseUrl(
            $fields['mod_markdown_url'] ?? null
        );
    }

    private function getBaseUrl(?string $url): string
    {
        if (empty($url)) {
            return '';
        }

        // Parse the URL and extract its path
        $parsedUrl = parse_url($url);

        // If no path exists, return the URL as it is
        if (!isset($parsedUrl['path'])) {
            return $url;
        }

        // Remove the filename by using pathinfo and keeping only the directory
        $pathInfo = pathinfo($parsedUrl['path']);
        $basePath = $pathInfo['dirname'];

        // Rebuild the URL without the filename
        $baseUrl = (isset($parsedUrl['scheme']) ? $parsedUrl['scheme'] . '://' : '') .
                (isset($parsedUrl['host']) ? $parsedUrl['host'] : '') .
                $basePath;

        return rtrim($baseUrl, '/') . '/';
    }

    /**
     * Filter the content.
     *
     * @param string $content The content to filter.
     *
     * @return string The filtered content.
     */
    public function filter(string $content): string
    {
        return preg_replace_callback('/<img src="([^"]+)"([^>]*)>/m', function ($matches) {
            $src = $matches[1];
            $attributes = $matches[2];

            // If the src is already an absolute URL, return the match as it is
            if (filter_var($src, FILTER_VALIDATE_URL)) {
                return "<img src=\"$src\"$attributes>";
            }

            // If the src is a relative URL, prepend the base URL
            return "<img src=\"{$this->baseUrl}$src\"$attributes>";
        }, $content);
    }
}