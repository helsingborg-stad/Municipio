<?php

namespace Municipio\PostTypeDesign;

use PHPUnit\Framework\TestCase;
use Municipio\PostTypeDesign\SetDesigns;
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

        $this->assertEquals('option_theme_mods_municipio', $wpService->calls['addFilter'][0][0]);
        $this->assertEquals('wp_get_custom_css', $wpService->calls['addFilter'][1][0]);
        $this->assertEquals('wp_head', $wpService->calls['addAction'][0][0]);
    }

    public function testSetCssReturnsOldCssIfNoPostType()
    {
        $wpService          = $this->getWpService(['getPostType' => false]);
        $setDesignsInstance = new SetDesigns('post_type_design', $wpService);

        $result = $setDesignsInstance->setCss('oldCss', 'stylesheet');

        $this->assertEquals('oldCss', $result);
    }

    public function testSetCssReturnsOldCssIfNoCorrespondingPostType()
    {
        $wpService          = $this->getWpService(['getPostType' => 'non-matching-post-type']);
        $setDesignsInstance = new SetDesigns('post_type_design', $wpService);

        $result = $setDesignsInstance->setCss('oldCss', 'stylesheet');

        $this->assertEquals('oldCss', $result);
    }

    public function testSetCssReturnsNewCssIfCorrespondingPostType()
    {
        $wpService          = $this->getWpService(['getPostType' => 'post']);
        $setDesignsInstance = new SetDesigns('post_type_design', $wpService);

        $result = $setDesignsInstance->setCss('oldCss', 'stylesheet');

        $this->assertEquals('newCssValue', $result);
    }

    public function testSetDesignReturnsValueIfNoPostTypeOrOption()
    {
        $wpService          = $this->getWpService(['getPostType' => '']);
        $setDesignsInstance = new SetDesigns('post_type_design', $wpService);

        $result = $setDesignsInstance->setDesign('value', 'option');

        $this->assertEquals('value', $result);
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

        $result = $setDesignsInstance->setDesign('value', 'post_type_design');

        $this->assertEquals('value1', $result['mod1']);
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

        $this->assertEmpty($output);
    }

    public function testInlineCssAddsIfNotEmpty()
    {
        $wpService = $this->getWpService([
            'getOption' => [
                'inlineCss' => 'css',
            ]
        ]);

        $setDesignsInstance = new SetDesigns('post_type_design', $wpService);
        ob_start();
        $setDesignsInstance->addInlineCss();
        $output = ob_get_clean();



        $this->assertTrue(str_contains($output, 'css'));
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
