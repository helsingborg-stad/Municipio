<?php

declare(strict_types=1);


namespace Municipio\PostTypeDesign;

use PHPUnit\Framework\TestCase;

use WpService\Contracts\AddFilter;
use WpService\Contracts\GetOption;
use WpService\Contracts\GetPostType;
use WP_Post;
use WpService\Contracts\AddAction;

class SetDesignsTest extends TestCase
{
    public function testFiltersAdded()
    {
        $wpService           = $this->getWpService();
        $saveDesignsInstance = new SetDesigns('post_type_design', $wpService);

        $saveDesignsInstance->addHooks();

        static::assertSame('option_theme_mods_municipio', $wpService->calls['addFilter'][0][0]);
        static::assertSame('wp_get_custom_css', $wpService->calls['addFilter'][1][0]);
        static::assertSame('wp_head', $wpService->calls['addAction'][0][0]);
    }

    public function testSetCssReturnsOldCssIfNoPostType()
    {
        $wpService          = $this->getWpService(['getPostType' => false]);
        $setDesignsInstance = new SetDesigns('post_type_design', $wpService);

        $result = $setDesignsInstance->setCss('oldCss', 'stylesheet');

        static::assertSame('oldCss', $result);
    }

    public function testSetCssReturnsOldCssIfNoCorrespondingPostType()
    {
        $wpService          = $this->getWpService(['getPostType' => 'non-matching-post-type']);
        $setDesignsInstance = new SetDesigns('post_type_design', $wpService);

        $result = $setDesignsInstance->setCss('oldCss', 'stylesheet');

        static::assertSame('oldCss', $result);
    }

    public function testSetCssReturnsNewCssIfCorrespondingPostType()
    {
        $wpService          = $this->getWpService(['getPostType' => 'post']);
        $setDesignsInstance = new SetDesigns('post_type_design', $wpService);

        $result = $setDesignsInstance->setCss('oldCss', 'stylesheet');

        static::assertSame('newCssValue', $result);
    }

    public function testSetDesignReturnsValueIfNoPostTypeOrOption()
    {
        $wpService          = $this->getWpService(['getPostType' => '']);
        $setDesignsInstance = new SetDesigns('post_type_design', $wpService);

        $result = $setDesignsInstance->setDesign('value', 'option');

        static::assertSame('value', $result);
    }

    public function testSetDesignReturnsOptionValueIfFound()
    {
        $wpService = $this->getWpService([
            'getPostType' => 'post',
            'getOption'   => [
                'post' => [
                    'design' => [
                        'mod1' => 'value1'
                    ]
                ]
            ]
        ]);

        $setDesignsInstance = new SetDesigns('post_type_design', $wpService);
        $setDesignsInstance::$cache = null; // Reset cache to ensure getOption is called

        $result = $setDesignsInstance->setDesign('value', 'post_type_design');

        static::assertSame('value1', $result['mod1']);
    }

    public function testInlineCssSkippedIfEmpty()
    {
        $wpService = $this->getWpService([
            'getOption' => [
                'inlineCss' => '',
            ]
        ]);

        $setDesignsInstance = new SetDesigns('post_type_design', $wpService);
        ob_start();
        $setDesignsInstance->addInlineCss();
        $output = ob_get_clean();

        static::assertEmpty($output);
    }

    public function testInlineCssAddsIfNotEmpty()
    {
        $wpService = $this->getWpService([
            'getOption' => [
                'inlineCss' => 'css',
            ]
        ]);

        $setDesignsInstance = new SetDesigns('post_type_design', $wpService);
        $setDesignsInstance::$cache = null; // Reset cache to ensure getOption is called
        ob_start();
        $setDesignsInstance->addInlineCss();
        $output = ob_get_clean();



        static::assertTrue(str_contains($output, 'css'));
    }


    private function getWpService(array $db = []): AddFilter&AddAction&GetOption&GetPostType
    {
        return new class ($db) implements AddFilter, AddAction, GetOption, GetPostType {
            public array $calls = ['addFilter' => [], 'addAction' => []];

            public function __construct(private array $db)
            {
            }

            public function addFilter(string $hookName, callable $callback, int $priority = 10, int $acceptedArgs = 1): true
            {
                $this->calls['addFilter'][] = func_get_args();
                return true;
            }

            public function addAction(string $hookName, callable $callback, int $priority = 10, int $acceptedArgs = 1): true
            {
                $this->calls['addAction'][] = func_get_args();
                return true;
            }

            public function getOption(string $option, mixed $defaultValue = false): mixed
            {
                return  $this->db['getOption'] ?? [
                    'post_type_design' => [
                        'post' => [
                            'css' => 'newCssValue'
                        ]
                    ]
                ][$option];
            }

            public function getPostType(int|WP_Post|null $post = null): string|false
            {
                return $this->db['getPostType'] ?? false;
            }
        };
    }
}
