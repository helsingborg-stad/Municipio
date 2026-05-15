<?php

namespace Municipio\Chat\Render;

interface ChatRenderInterface {
    public function render(ChatRenderConfigInterface $renderConfig): string;
}