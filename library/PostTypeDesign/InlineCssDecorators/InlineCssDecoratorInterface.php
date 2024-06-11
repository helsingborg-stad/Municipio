<?php

namespace Municipio\PostTypeDesign\InlineCssDecorators;

interface InlineCssDecoratorInterface
{
    public function decorate(array $inlineCss): array;
}
