<?php

namespace Municipio\Styleguide\Customize\ApplyStyles\DesignTokensToCssConverter;

interface DesignTokensToCssConverterInterface
{
    public function convert(array $designTokens): string;
}
