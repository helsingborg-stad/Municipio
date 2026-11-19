<?php

declare(strict_types=1);

namespace Municipio\Chat\Admin;

use AcfService\Implementations\FakeAcfService;
use Municipio\HooksRegistrar\Hookable;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WP_Post_Type;
use WpService\Implementations\FakeWpService;

class ChatAdminPageTest extends TestCase
{
    #[TestDox('implements Hookable')]
    public function testImplementsHookable(): void
    {
        static::assertInstanceOf(Hookable::class, new ChatAdminPage($this->getWpService(), new FakeAcfService()));
    }

    #[TestDox('addHooks() registers the options page on init and filters the assistant pages field')]
    public function testAddHooksRegistersHooks(): void
    {
        $wpService = $this->getWpService();
        $page = new ChatAdminPage($wpService, new FakeAcfService());

        $page->addHooks();

        static::assertSame('init', $wpService->methodCalls['addAction'][0][0]);
        static::assertSame([$page, 'register'], $wpService->methodCalls['addAction'][0][1]);
        static::assertSame('acf/fields/post_object/query/name=chat_assistant_pages', $wpService->methodCalls['addFilter'][0][0]);
        static::assertSame([$page, 'filterAssistantPagesField'], $wpService->methodCalls['addFilter'][0][1]);
    }

    #[TestDox('register() adds the chat-settings options page')]
    public function testRegisterAddsOptionsPage(): void
    {
        $acfService = new FakeAcfService();
        $page = new ChatAdminPage($this->getWpService(), $acfService);

        $page->register();

        static::assertCount(1, $acfService->methodCalls['addOptionsPage']);
        $options = $acfService->methodCalls['addOptionsPage'][0][0];
        static::assertSame('chat-settings', $options['menu_slug']);
        static::assertSame('edit_posts', $options['capability']);
        static::assertSame('Chat Settings', $options['page_title']);
        static::assertSame('Chat Settings', $options['menu_title']);
        static::assertSame('dashicons-format-chat', $options['icon_url']);
    }

    #[TestDox('filterAssistantPagesField() keeps only existing, hierarchical and viewable post types')]
    public function testFilterKeepsOnlyHierarchicalViewablePostTypes(): void
    {
        $wpService = $this->getWpService([
            'getPostTypeObject' => static fn(string $postType) => $postType === 'missing' ? null : self::postTypeObject($postType),
            'isPostTypeHierarchical' => static fn(string $postType) => in_array($postType, ['page', 'private_page', 'missing'], true),
            'isPostTypeViewable' => static fn(WP_Post_Type $postTypeObject) => $postTypeObject->name !== 'private_page',
        ]);
        $page = new ChatAdminPage($wpService, new FakeAcfService());

        $result = $page->filterAssistantPagesField([
            'post_type' => ['post', 'page', 'private_page', 'missing'],
            'posts_per_page' => 20,
        ]);

        static::assertSame(['page'], array_values($result['post_type']));
        static::assertSame(20, $result['posts_per_page']);
    }

    #[TestDox('filterAssistantPagesField() returns an empty post_type list when none is given')]
    public function testFilterHandlesMissingPostType(): void
    {
        $page = new ChatAdminPage($this->getWpService(), new FakeAcfService());

        static::assertSame(['post_type' => []], $page->filterAssistantPagesField([]));
    }

    private static function postTypeObject(string $name): WP_Post_Type
    {
        $postType = new WP_Post_Type([]);
        $postType->name = $name;
        return $postType;
    }

    private function getWpService(array $returnValues = []): FakeWpService
    {
        return new FakeWpService(array_merge([
            '__' => static fn(string $text) => $text,
            'addAction' => true,
            'addFilter' => true,
        ], $returnValues));
    }
}
