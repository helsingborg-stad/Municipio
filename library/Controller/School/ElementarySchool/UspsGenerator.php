<?php

namespace Municipio\Controller\School\ElementarySchool;

use Municipio\Controller\School\ViewDataGeneratorInterface;
use Municipio\Schema\ElementarySchool;
use Municipio\Schema\OpeningHoursSpecification;
use WpService\Contracts\_x;
use WpService\Contracts\GetTheTerms;

class UspsGenerator implements ViewDataGeneratorInterface
{
    public function __construct(
        private ElementarySchool $elementarySchool,
        private int $postId,
        private GetTheTerms&_x $wpService
    ) {
    }

    public function generate(): mixed
    {
        return $this->splitArrayIntoColumns(array_merge(
            $this->getTerms(),
            $this->getAreaServed(),
            $this->getNumberOfStudents(),
            $this->getAfterSchoolCareOpeningHours()
        ), 3);
    }

    private function getAfterSchoolCareOpeningHours(): array
    {
        $afterSchoolCare = $this->elementarySchool->getProperty('afterSchoolCare');
        $openingHours    = $afterSchoolCare?->getProperty('hoursAvailable');

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
        $label            = $this->wpService->_x('After school care opening hours: %s', 'ElementarySchool', 'municipio');
        $label            = sprintf($label, $openingHoursText);
        return [$label];
    }

    private function getNumberOfStudents(): array
    {
        if (!is_numeric($this->elementarySchool->getProperty('numberOfStudents'))) {
            return [];
        }

        return [sprintf($this->wpService->_x('Ca. %s students', 'ElementarySchool', 'municipio'), $this->elementarySchool->getProperty('numberOfStudents'))];
    }

    private function getAreaServed(): array
    {
        $areaServed = $this->elementarySchool->getProperty('areaServed');

        if (!is_array($areaServed) || empty($areaServed)) {
            return [];
        }

        return array_filter($areaServed, fn ($item) => is_string($item) && !empty($item));
    }

    private function getTerms(): array
    {
        $terms = $this->wpService->getTheTerms($this->postId, 'elementary_school_keywords_name');

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
