<?php
namespace Municipio\Chat\Render\Integration;

class Eneo implements RenderIntegrationInterface {
    public function __construct()
    {
    }

    public function transform(array $data, array $assistant): array
    {
        if (isset($assistant['integrationData'])) {
            $data['integrationData'] = $assistant['integrationData'];
        }

        return $data;
    }
}