<?php
namespace Municipio\Chat\Render\Integration;

use Municipio\Chat\Render\AssistantKey;

class Eneo implements RenderIntegrationInterface {
    public function __construct()
    {
    }

    public function transform(array $data, array $assistant): array
    {
        $attributePrefix = $this->getAttributePrefix($assistant);
        $greetingPhrase = $assistant['greetings_phrase'] ?? '';
        $greetingPhrase = str_replace(["\r\n", "\r", "\n"], '&#10;', $greetingPhrase);

        $data['attributeList'] = $data['attributeList'] ?? [];
        $data['attributeList'] = array_merge($data['attributeList'], [
            $attributePrefix . 'name' => $assistant['name'] ?? '',
            $attributePrefix . 'greeting-phrase' => $greetingPhrase,
            $attributePrefix . 'avatar' => $assistant['avatar'] ?? '',
        ]);

        return $data;
    }

    private function getAttributePrefix(array $assistant): string
    {
        return AssistantKey::attributePrefix((string) ($assistant['name'] ?? ''));
    }
}