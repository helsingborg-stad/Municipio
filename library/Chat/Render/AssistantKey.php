<?php

declare(strict_types=1);

namespace Municipio\Chat\Render;

/**
 * Builds a safe identifier for an assistant, used in HTML attribute names
 * (data-js-chat-{key}-*) and as the key in the data-js-chat-assistants JSON.
 *
 * Attribute names are not escaped when rendered, so the key must only ever
 * contain letters, digits and dashes.
 */
class AssistantKey
{
    public static function fromName(string $name): string
    {
        $key = mb_strtolower($name, 'UTF-8');
        $key = preg_replace('/[^\p{L}\p{N}]+/u', '-', $key) ?? '';
        $key = trim($key, '-');

        if ($key === '') {
            return 'assistant-' . substr(md5($name), 0, 8);
        }

        return $key;
    }

    public static function attributePrefix(string $name): string
    {
        return 'data-js-chat-' . self::fromName($name) . '-';
    }
}
