<?php

namespace Municipio\PostObject\TermIcon;

interface TermIconInterface
{
    /**
     * Get the term ID.
     */
    public function getTermId(): int;

    /**
     * Get the term icon.
     */
    public function getIcon(): string;

    /**
     * Get the term color.
     */
    public function getColor(): string;
}
