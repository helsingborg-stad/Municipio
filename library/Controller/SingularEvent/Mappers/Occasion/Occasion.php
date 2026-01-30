<?php

namespace Municipio\Controller\SingularEvent\Mappers\Occasion;

class Occasion implements OccasionInterface
{
    public function __construct(
        private string $startDate,
        private string $endDate,
        private bool $isCurrent,
        private string $url,
    ) {}

    public function getStartDate(): string
    {
        return $this->startDate;
    }

    public function getEndDate(): string
    {
        return $this->endDate;
    }

    public function getEndTime(): string
    {
        return strtotime($this->endDate) ? date('H:i:s', strtotime($this->endDate)) : '';
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
