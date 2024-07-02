<?php

namespace Municipio\IniService;

interface IniServiceInterface
{
    public function get(string $key): string;
    public function set(string $key, string $value): void;
}
