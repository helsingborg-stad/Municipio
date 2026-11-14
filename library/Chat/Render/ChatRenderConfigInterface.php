<?php

namespace Municipio\Chat\Render;

interface ChatRenderConfigInterface
{
    public function getView(): string;
    public function getAssistants(): array;
    public function getWrapperAttributes(): ?string;
    public function getChatId(): string;
    public function getAttributeList(): array;
    public function getLang(): array;
}
