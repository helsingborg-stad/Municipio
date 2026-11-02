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

        echo '<pre>' . print_r( $assistant, true ) . '</pre>';die;

        return sprintf(
            '<div %s>%s</div>',
            $renderConfig->getWrapperAttributes(),
            $this->bladeRenderer->render($renderConfig->getView(), $data),
        );
    }

    public static function getViewPathsDir(): array
    {
        return [__DIR__ . '/views/'];
    }
}
