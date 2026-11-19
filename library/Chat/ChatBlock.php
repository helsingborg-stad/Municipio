<?php

namespace Municipio\Chat;

use Municipio\Chat\Config\ChatConfigInterface;
use Municipio\Chat\Render\ChatRenderConfig;
use Municipio\Chat\Render\ChatRenderInterface;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\__;
use WpService\Contracts\AddAction;
use WpService\Contracts\GetBlockWrapperAttributes;
use WpService\Contracts\RegisterBlockType;

class ChatBlock implements Hookable
{
    private const UNSUPPORTED_INTEGRATIONS = ['puzzel'];

    public function __construct(
        private RegisterBlockType&AddAction&__&GetBlockWrapperAttributes $wpService,
        private ChatConfigInterface $config,
        private ChatRenderInterface $renderer,
    ) {}

    public function addHooks(): void
    {
        $this->wpService->addAction('init', [$this, 'registerBlock']);
    }

    public function registerBlock()
    {
        $assistants = $this->getBlockAssistants();

        if (empty($assistants)) {
            return;
        }

        $result = array_column($assistants, 'name', 'name');
        $result = array_merge(['Default' => __('Default', 'municipio')], $result);

        $this->wpService->registerBlockType(
            'municipio/chat',
            [
                'title' => __('Chat', 'municipio'),
                'description' => __('A simple chat block.', 'municipio'),
                'attributes' => [
                    'assistant' => [
                        'label' => __('Assistant', 'municipio'),
                        'type' => 'string',
                        'enum' => array_keys($result),
                        'default' => 'Default',
                    ],
                ],
                'render_callback' => function ($attributes) {
                    $assistant = $this->resolveAssistant($attributes['assistant'] ?? 'Default');

                    if (
                        $assistant === null
                        || !$this->isSupportedInBlock($assistant)
                        || !$this->config->isAssistantAllowed($assistant)
                    ) {
                        return '';
                    }

                    $config = new ChatRenderConfig(
                        $this->wpService,
                        'block',
                        [$assistant],
                        $this->wpService->getBlockWrapperAttributes(),
                    );

                    return $this->renderer->render($config);
                },
                'supports' => array(
                    'autoRegister' => true,
                ),
            ],
        );
    }

    private function resolveAssistant(string $assistantName): ?array
    {
        if ($assistantName === '' || $assistantName === 'Default') {
            return $this->config->getDefaultAssistant();
        }

        foreach ($this->getBlockAssistants() as $assistant) {
            if (($assistant['name'] ?? null) === $assistantName) {
                return $assistant;
            }
        }

        return null;
    }

    /**
     * Assistants that can be used in the block. Puzzel is excluded because its
     * conversation state is global and belongs to the chat bubble.
     */
    private function getBlockAssistants(): array
    {
        return array_values(array_filter(
            $this->config->getAssistants(),
            fn($assistant) => is_array($assistant) && $this->isSupportedInBlock($assistant),
        ));
    }

    private function isSupportedInBlock(array $assistant): bool
    {
        return !in_array($assistant['acf_fc_layout'] ?? null, self::UNSUPPORTED_INTEGRATIONS, true);
    }
}
