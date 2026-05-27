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

        return sprintf(
            '<div %s>%s</div>',
            $renderConfig->getWrapperAttributes(),
            $this->bladeRenderer->render($renderConfig->getView(), [
                'lang' => $renderConfig->getLang(),
                'avatar' => $renderConfig->getAvatar(),
                'name' => $assistant['name'],
                'attributeList' => $renderConfig->getAttributeList()
            ]),
        );
    }

    public static function getViewPathsDir(): array
    {
        return [__DIR__ . '/views/'];
    }
}
