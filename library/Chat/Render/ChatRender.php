<?php

namespace Municipio\Chat\Render;

use ComponentLibrary\Renderer\RendererInterface as BladeRenderInterface;

class ChatRender implements ChatRenderInterface
{
    public function __construct(
        private BladeRenderInterface $bladeRenderer,
    ) {}

    public function render(ChatRenderConfigInterface $renderConfig): string
    {  
        if (empty($renderConfig->getAssistants())) {
            return '';
        }

        $data = [
            'lang' => $renderConfig->getLang(),
            'chatId' => $renderConfig->getChatId(),
            'attributeList' => $renderConfig->getAttributeList()
        ];

        foreach ($renderConfig->getAssistants() as $assistant) {
            $data = $this->addIntegrationData($assistant, $data);
        }

        return sprintf(
            '<div %s>%s</div>',
            $renderConfig->getWrapperAttributes(),
            $this->bladeRenderer->render($renderConfig->getView(), $data),
        );
    }

    private function getIntegrationClass(array $assistant): string
    {
        return "Municipio\Chat\Render\Integration\\" . ucfirst($assistant['acf_fc_layout'] ?? "");
    }

    private function addIntegrationData(array $assistant, array $data): array
    {
        $class = $this->getIntegrationClass($assistant);
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
