<?php

namespace Municipio\Controller\SingularEvent\Mappers\Occasion;

interface OccasionInterface
{
    public function getDateTime(): string;
    public function isCurrent(): bool;
    public function getUrl(): string;
}
