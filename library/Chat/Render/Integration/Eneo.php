<?php
namespace Municipio\Chat\Render\Integration;

class Eneo implements RenderIntegrationInterface {
    public function __construct()
    {
    }

    public function transform(array $data, array $assistant): array
    {
        $attributePrefix = $this->getAttributePrefix($assistant);

        $data['attributeList'] = $data['attributeList'] ?? [];
        $data['attributeList'] = array_merge($data['attributeList'], [
            $attributePrefix . 'name' => $assistant['name'] ?? '',
        ]);

        return $data;
    }

    private function getAttributePrefix(array $assistant): string
    {
        return 'data-js-chat-' . strtolower($assistant['name']) . '-';
    }
}