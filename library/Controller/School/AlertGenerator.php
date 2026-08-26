<?php

declare(strict_types=1);


namespace Municipio\Controller\School;

use Municipio\Helper\EnsureArrayOf\EnsureArrayOf;
use Municipio\Schema\ElementarySchool;
use Municipio\Schema\Preschool;
use Municipio\Schema\TextObject;

class AlertGenerator
{
    public function __construct(private ElementarySchool|Preschool $school)
    {
    }

    public function generate(): mixed
    {
        return $this->getAlertFromDescription($this->school->getProperty('description'));
    }

    private function getAlertFromDescription(array|string|TextObject|null $description): ?array
    {
        if (is_array($description)) {
            return $this->getAlertFromDescriptionArray(...EnsureArrayOf::ensureArrayOf($description, TextObject::class));
        }

        return null;
    }

    private function getAlertFromDescriptionArray(TextObject ...$description): ?array
    {
        foreach ($description as $textObject) {
            if (
                $textObject->getProperty('name') === 'role:alert' && $textObject->getProperty('headline') && $textObject->getProperty('text')) {
                return [
                    'title' => $textObject->getProperty('headline'),
                    'text' => $textObject->getProperty('text'),
                ];
            }
        }

        return null;
    }
}
