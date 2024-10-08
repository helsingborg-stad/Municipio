<?php

namespace Municipio\Controller\Navigation\Config;

interface MenuConfigInterface {
    public function getIdentifier(): string;
    public function getMenuName(): string;
    public function getRemoveSubLevels(): bool;
    public function getRemoveTopLevel(): bool;
    public function getFallbackToPageTree(): bool|int;
}