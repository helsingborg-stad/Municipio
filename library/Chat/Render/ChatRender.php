<?php

namespace Municipio\Chat\Render;

use ComponentLibrary\Renderer\RendererInterface as BladeRenderInterface;

class ChatRender implements ChatRenderInterface
{
    public function __construct(
        private BladeRenderInterface $bladeRenderer,
    ) {}

    public function render(ChatRenderConfigInterface $renderConfig): string
    {;
        $assistant = $renderConfig->getAssistant();
        
        if (empty($assistant)) {
            return '';
        }

        $data = [
            'lang' => $renderConfig->getLang(),
            'avatar' => $renderConfig->getAvatar(),
            'name' => $assistant['name'],
            'chatId' => $renderConfig->getChatId(),
            'attributeList' => $renderConfig->getAttributeList()
        ];

        $data = $this->addIntegrationData($assistant, $data);

        return sprintf(
            '<div %s>%s</div>',
            $renderConfig->getWrapperAttributes(),
            $this->bladeRenderer->render($renderConfig->getView(), $data),
        );
    }

    private function addIntegrationData(array $assistant, array $data): array
    {
        $class = "Municipio\Chat\Render\Integration\\" . ucfirst($assistant['acf_fc_layout'] ?? "");

        if (class_exists($class)) {
            $data = (new $class())->transform($data, $assistant);
        }

        return $data;
    }

    public static function getViewPathsDir(): array
    {
        return [__DIR__ . '/views/'];
    }
}
