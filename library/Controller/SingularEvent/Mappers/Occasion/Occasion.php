<?php

namespace Municipio\Controller\SingularEvent\Mappers\Occasion;

class Occasion implements OccasionInterface
{
    public function __construct(private string $dateTime, private bool $isCurrent, private string $url)
    {
    }

    public function getDateTime(): string
    {
        return $this->dateTime;
    }

    public function isCurrent(): bool
    {
        return $this->isCurrent;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
