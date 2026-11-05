<?php
namespace Municipio\Chat\Render\Integration;

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
        ]);

        return $data;
    }

    private function getAttributePrefix(array $assistant): string
    {
        return 'data-js-chat-' . strtolower($assistant['name']) . '-';
    }
}