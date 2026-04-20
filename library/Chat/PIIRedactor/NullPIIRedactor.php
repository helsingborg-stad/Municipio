<?php

namespace Municipio\Chat\PIIRedactor;

class NullPIIRedactor implements PIIRedactorInterface
{
    public function extractAndRedactPII(string $input): RedactionResult
    {
        $result = new RedactionResult();
        $result->redactedText = $input;
        $result->mappedPII = [];
        return $result;
    }
}
