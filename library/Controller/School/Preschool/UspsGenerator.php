<?php

namespace Municipio\Controller\School\Preschool;

use Municipio\Controller\School\ViewDataGeneratorInterface;
use Municipio\Schema\Preschool;
use Municipio\Schema\OpeningHoursSpecification;
use WpService\Contracts\_nx;
use WpService\Contracts\_x;
use WpService\Contracts\GetTheTerms;

class UspsGenerator implements ViewDataGeneratorInterface
{
    public function __construct(
        private Preschool $preschool,
        private int $postId,
        private GetTheTerms&_x&_nx $wpService
    ) {
    }

    public function generate(): mixed
    {
        return $this->splitArrayIntoColumns(array_merge(
            $this->getTerms(),
            $this->getAreaServed(),
            $this->getNumberOfChildren(),
            $this->getNumberOfGroups(),
            $this->getOpeningHours()
        ), 3);
    }

    private function getOpeningHours(): array
    {
        $openingHours = $this->preschool->getProperty('openingHoursSpecification');

        if (!is_a($openingHours, OpeningHoursSpecification::class)) {
            return [];
        }

        /**
         * @var DateTime|null $opens
         */
        $opens = $openingHours->getProperty('opens');
        /**
         * @var DateTime|null $closes
         */
        $closes = $openingHours->getProperty('closes');

        if (empty($opens) || empty($closes)) {
            return [];
        }

        $opens            = $opens->format('H:i');
        $closes           = $closes->format('H:i');
        $openingHoursText = "{$opens}-{$closes}";
        $label            = $this->wpService->_x('Opening hours: %s', 'Preschool', 'municipio');
        $label            = sprintf($label, $openingHoursText);
        return [$label];
    }

    private function getNumberOfChildren(): array
    {
        if (!is_numeric($this->preschool->getProperty('numberOfChildren'))) {
            return [];
        }

        return [sprintf($this->wpService->_x('Ca. %s children', 'Preschool', 'municipio'), $this->preschool->getProperty('numberOfChildren'))];
    }

    private function getNumberOfGroups(): array
    {
        if (!is_numeric($this->preschool->getProperty('numberOfGroups'))) {
            return [];
        }

        return [sprintf(
            $this->wpService->_nx('%s group', '%s groups', $this->preschool->getProperty('numberOfGroups'), 'Preschool', 'municipio'),
            $this->preschool->getProperty('numberOfGroups')
        )];
    }

    private function getAreaServed(): array
    {
        $areaServed = $this->preschool->getProperty('areaServed');

        if (!is_array($areaServed) || empty($areaServed)) {
            return [];
        }

        return array_filter($areaServed, fn ($item) => is_string($item) && !empty($item));
    }

    private function getTerms(): array
    {
        $terms = $this->wpService->getTheTerms($this->postId, 'preschool_keywords_name');

        if (!is_array($terms) || empty($terms)) {
            return [];
        }

        $terms = array_filter($terms, fn ($term) => is_a($term, \WP_Term::class));
        return array_map(fn ($term) => $term->name, $terms);
    }

    public function splitArrayIntoColumns(array $array, int $columns): array
    {
        if ($columns < 1) {
            return [$array];
        }

        $chunkSize = (int) ceil(count($array) / $columns);
        return array_chunk($array, $chunkSize);
    }
}
