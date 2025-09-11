<?php

namespace Municipio\Controller\SingularElementarySchool;

use Municipio\Schema\ElementarySchool;
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
            $this->getNumberOfStudents()
        ), 3);
    }

    private function getNumberOfStudents(): array
    {
        if (is_array($this->elementarySchool->getProperty('additionalProperty'))) {
            foreach ($this->elementarySchool->getProperty('additionalProperty') as $property) {
                if (
                    is_a($property, \Municipio\Schema\PropertyValue::class) &&
                    $property->getProperty('name') === 'number_of_students' &&
                    is_numeric($property->getProperty('value')) &&
                    !empty($property->getProperty('value'))
                ) {
                    return [
                        sprintf($this->wpService->_x('Ca. %s students', 'ElementarySchool', 'municipio'), $property->getProperty('value'))
                    ];
                }
            }
        }

        return [];
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
