<?php

declare(strict_types=1);

namespace Modularity;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddFilter;

/**
 * Extends Municipio SearchIndex records with bundled Modularity content.
 */
class SearchIndex implements Hookable
{
    public function __construct(private AddFilter $wpService)
    {
    }

    public function addHooks(): void
    {
        $this->wpService->addFilter('Municipio/SearchIndex/Record/Content', [$this, 'addModuleContent'], 10, 2);
        $this->wpService->addFilter('Municipio/SearchIndex/IndexablePostTypes', [$this, 'removeModulePostTypes']);
    }

    public function addModuleContent(string $content, int $postId): string
    {
        $moduleContent = $this->getRenderedPostModules($postId);
        return $moduleContent === '' ? $content : trim($content . ' ' . $moduleContent);
    }

    /**
     * @param array<int, string> $postTypes
     * @return array<int, string>
     */
    public function removeModulePostTypes(array $postTypes): array
    {
        return array_values(array_filter(
            $postTypes,
            static fn(string $postType): bool => !str_starts_with($postType, 'mod-'),
        ));
    }

    public function getRenderedPostModules(int $postId): string
    {
        $moduleContent = [];

        foreach (Editor::getPostModules($postId) as $moduleArea) {
            foreach ($moduleArea['modules'] ?? [] as $module) {
                if (!$module instanceof \WP_Post || $module->post_type === 'mod-wpwidget') {
                    continue;
                }

                $markup = App::$display?->outputModule($module, ['edit_module' => false], [], false);

                if (is_string($markup) && $markup !== '') {
                    $moduleContent[] = $this->normalizeText($markup);
                }
            }
        }

        return trim(implode(' ', array_filter($moduleContent)));
    }

    private function normalizeText(string $markup): string
    {
        return trim(preg_replace('/\s+/', ' ', strip_tags($markup)) ?? '');
    }
}