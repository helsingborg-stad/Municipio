<?php

namespace Municipio\Chat\Render;

interface ChatRenderConfigInterface
{
    public function getView(): string;
    public function getAssistant(): ?array;
    public function getWrapperAttributes(): ?string;
    public function getAssistantName(): string;
    public function getAvatar(): ?array;
    public function getGreetingsPhrase(): ?string;
    public function getAttributeList(): array;
    public function getLang(): array;
}