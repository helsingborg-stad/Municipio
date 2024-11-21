<?php 

namespace Municipio\Customizer\Applicators;

interface ApplicatorInterface
{
    public function getKey(): string;
    public function getData(): array|object|string;
    public function applyData(array|object $data);
}