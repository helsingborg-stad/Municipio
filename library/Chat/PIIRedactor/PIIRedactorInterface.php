<?php

namespace Municipio\Chat\PIIRedactor;

interface PIIRedactorInterface
{
    /**
     * Extract and redact personally identifiable information (PII) from the input string.
     *
     * @param string $input The input string potentially containing PII.
     * @return RedactionResult The result containing the redacted text.
     */
    public function extractAndRedactPII(string $input): RedactionResult;
}
