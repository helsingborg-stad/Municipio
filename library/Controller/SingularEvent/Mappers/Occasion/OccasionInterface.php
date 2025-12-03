<?php

namespace Municipio\Controller\SingularEvent\Mappers\Occasion;

interface OccasionInterface
{
    public function getStartDate(): string;
    public function getEndDate(): string;
    public function isCurrent(): bool;
    public function getUrl(): string;
}
