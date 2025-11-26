<?php

namespace Municipio\SingleDigitalGateway;

class MetaTag implements \Stringable
{
    public function __construct(
        private string $name,
        private string $content,
    ) {}

    public function __toString(): string
    {
        return sprintf(
            '<meta name="%s" content="%s">',
            htmlspecialchars($this->name, ENT_QUOTES),
            htmlspecialchars($this->content, ENT_QUOTES),
        );
    }
}
