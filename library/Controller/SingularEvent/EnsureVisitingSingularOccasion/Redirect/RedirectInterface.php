<?php

namespace Municipio\Controller\SingularEvent\EnsureVisitingSingularOccasion\Redirect;

interface RedirectInterface
{
    public function redirect(string $url): void;
}
