<?php

namespace Municipio\Controller\SingularEvent\Mappers;

use Municipio\Schema\Event;
use Municipio\Schema\TextObject;
use WpService\Contracts\Wpautop;

/**
 * Maps the event description to formatted text.
 */
class MapDescription implements EventDataMapperInterface
{
    /**
     * Constructor
     */
    public function __construct(private Wpautop $wpService)
    {
    }

    /**
     * Maps the event description to formatted text.
     *
     * @param Event $event
     * @return string
     */
    public function map(Event $event): string
    {
        $description = $event->getProperty('description');

        if (empty($description) || !is_array($description)) {
            return '';
        }

        $descriptionText = implode(
            '',
            array_filter(
                array_map([self::class, 'sanitizeText'], $description)
            )
        );

        return $this->wpService->wpautop($descriptionText);
    }

    /**
     * Sanitizes a text value or TextObject.
     *
     * @param mixed $text
     * @return string|null
     */
    private static function sanitizeText(mixed $text): ?string
    {
        if (is_string($text)) {
            return $text;
        } elseif (is_a($text, TextObject::class)) {
            return $text->getProperty('text') ?? null;
        }

        return null;
    }
}
