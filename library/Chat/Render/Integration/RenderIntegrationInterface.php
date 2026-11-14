<?php

namespace Municipio\Chat\Render\Integration;

interface RenderIntegrationInterface {
    public function transform(array $data, array $assistant): array;
}