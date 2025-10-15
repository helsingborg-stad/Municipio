<?php 

namespace Modularity\Module\Markdown\Filters;

use Modularity\Module\Markdown\Filters\FilterInterface;

class DemoteTitles implements FilterInterface
{
    public function __construct(private array $fields)
    {   
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
        return preg_replace_callback('/^(#+)(.*)/m', function ($matches) {
            return str_repeat('#', strlen($matches[1]) + 1) . $matches[2];
        }, $content);
    }
}