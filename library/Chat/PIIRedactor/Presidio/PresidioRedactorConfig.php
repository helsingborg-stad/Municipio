<?php

namespace Municipio\Chat\PIIRedactor\Presidio;

class PresidioRedactorConfig
{
    public function __construct(
        public readonly ?string $presidioAnalyzeHost,
        public readonly ?string $presidioAnonymizeHost = null,
        public readonly ?string $language = null,
        public readonly ?array $anonymizerConfig = null,
        public readonly array $allowList = [],
    ) {}
}
