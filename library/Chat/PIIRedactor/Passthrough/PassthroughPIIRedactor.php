<?php

namespace Municipio\Chat\PIIRedactor\Passthrough;

use Municipio\Chat\PIIRedactor\PIIRedactorInterface;
use Municipio\Chat\PIIRedactor\RedactionResult;

class PassthroughPIIRedactor implements PIIRedactorInterface
{
    public function extractAndRedactPII(string $input): RedactionResult
    {
        $result = new RedactionResult();
        $result->redactedText = $input;
        return $result;
    }
}
