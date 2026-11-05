<?php

namespace Municipio\Chat\Render\Integration;

class Puzzel implements RenderIntegrationInterface {
    public function transform(array $data, array $assistant): array
    {
        $attributePrefix = $this->getAttributePrefix($assistant);

        $data['attributeList'] = $data['attributeList'] ?? [];
        $data['attributeList'] = array_merge($data['attributeList'], [
            $attributePrefix . 'customer-id' => $assistant['customer_id'] ?? '',
            $attributePrefix . 'queue-key' => $assistant['queue_key'] ?? '',
            $attributePrefix . 'visual-queue-id' => $assistant['visual_queue_id'] ?? '',
            $attributePrefix . 'time-id' => $assistant['time_id'] ?? '',
            $attributePrefix . 'client-id' => $assistant['client_id'] ?? '',
            $attributePrefix . 'name' => $assistant['name'] ?? '',
        ]);

        return $data;
    }

    private function getAttributePrefix(array $assistant): string
    {
        return 'data-js-chat-' . strtolower($assistant['name']) . '-';
    }
}