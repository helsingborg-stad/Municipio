<?php

namespace Municipio\Styleguide\Customize\DesignTokensToCssConverter;

interface DesignTokensToCssConverterInterface
{
    public function convert(array $designTokens): string;
}
