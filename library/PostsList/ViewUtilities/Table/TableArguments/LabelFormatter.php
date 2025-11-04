<?php

namespace Municipio\PostsList\ViewUtilities\Table\TableArguments;

use WpService\Contracts\WpDate;
use WpService\Contracts\GetOption;

/**
 * Formats labels such as term names, converting date-like strings to formatted dates
 */
class LabelFormatter implements LabelFormatterInterface
{
    /**
     * Constructor
     */
    public function __construct(
        private WpDate&GetOption $wpService
    ) {
    }

    /**
     * Format a term name, converting date-like strings to formatted dates
     *
     * @param string $name
     * @return string
     */
    public function formatTermName(string $name): string
    {
        $name         = trim($name);
        $datePatterns = [
            '/^\d{4}-\d{2}-\d{2}$/',         // YYYY-MM-DD
            '/^\d{2}\/\d{2}\/\d{4}$/',       // DD/MM/YYYY or MM/DD/YYYY
            '/^\d{2}-\d{2}-\d{4}$/',         // DD-MM-YYYY
            '/^\w+ \d{4}$/',                 // "Month YYYY"
            '/^\d{1,2} \p{L}+, \d{4}$/u',    // "30 january, 2025"
            '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\+\d{2}:\d{2}$/', // "2025-11-09T00:00:00+00:00"
        ];
        foreach ($datePatterns as $pattern) {
            if (preg_match($pattern, $name)) {
                $timestamp = strtotime($name);
                if ($timestamp && $timestamp > 0) {
                    static $dateFormat = null;

                    if ($dateFormat === null) {
                        $dateFormat = $this->wpService->getOption('date_format');
                    }

                    return $this->wpService->wpDate($dateFormat, $timestamp);
                }
            }
        }
        return $name;
    }
}
