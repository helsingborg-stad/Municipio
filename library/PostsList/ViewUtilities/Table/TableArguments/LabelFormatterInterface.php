<?php

namespace Municipio\PostsList\ViewUtilities\Table\TableArguments;

interface LabelFormatterInterface
{
    /**
     * Format a term name, converting date-like strings to formatted dates
     *
     * @param string $name The term name to format
     * @return string The formatted term name
     */
    public function formatTermName(string $name): string;
}
