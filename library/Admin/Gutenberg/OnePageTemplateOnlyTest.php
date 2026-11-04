<?php

namespace Municipio\Admin\Gutenberg;

use AcfService\Implementations\FakeAcfService;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class OnePageTemplateOnlyTest extends TestCase
{
    private FakeWpService $wpService;

    private function createSut(string $mode = 'all', string $postType = 'page'): OnePageTemplateOnly
    {
        $this->wpService = new FakeWpService([
            'addAction'    => true,
            'addFilter'    => true,
            'applyFilters' => fn ($hookName, $value) => $value,
            'getPostType'  => fn () => $postType,
        ]);

        return new OnePageTemplateOnly($this->wpService, new FakeAcfService(['getField' => fn () => $mode]));
    }

    #[TestDox('addHooks() registers hooks')]
    public function testAddHooks(): void
    {
        $sut = $this->createSut();
        $sut->addHooks();

        $this->assertEquals('init', $this->wpService->methodCalls['addAction'][0][0]);
        $this->assertEquals('get_post_metadata', $this->wpService->methodCalls['addFilter'][0][0]);
        $this->assertEquals('block_editor_settings_all', $this->wpService->methodCalls['addFilter'][1][0]);
    }

    #[TestDox('limitTemplates() only keeps the one-page template when mode is all')]
    public function testLimitTemplates(): void
    {
        $templates = ['page-centered.blade.php' => 'Centered', 'one-page.blade.php' => 'One Page'];

        $this->assertEquals(['one-page.blade.php' => 'One Page'], $this->createSut()->limitTemplates($templates));
        $this->assertEquals($templates, $this->createSut('template')->limitTemplates($templates));
    }

    #[TestDox('resolveTemplateMeta() resolves page template as one-page when mode is all')]
    public function testResolveTemplateMeta(): void
    {
        $this->assertEquals(['one-page.blade.php'], $this->createSut()->resolveTemplateMeta(null, 1, '_wp_page_template', true));
        $this->assertNull($this->createSut()->resolveTemplateMeta(null, 1, 'other_key', true));
        $this->assertNull($this->createSut('template')->resolveTemplateMeta(null, 1, '_wp_page_template', true));
        $this->assertNull($this->createSut('all', 'post')->resolveTemplateMeta(null, 1, '_wp_page_template', true));
    }

    #[TestDox('removeDefaultTemplateOption() only keeps the one-page template in the block editor')]
    public function testRemoveDefaultTemplateOption(): void
    {
        $post            = new \WP_Post([]);
        $post->post_type = 'page';
        $context         = new \stdClass();
        $context->post   = $post;
        $settings        = ['availableTemplates' => ['' => 'Default', 'one-page.blade.php' => 'One Page']];

        $result = $this->createSut()->removeDefaultTemplateOption($settings, $context);

        $this->assertEquals(['one-page.blade.php' => 'One Page'], $result['availableTemplates']);
    }
}
