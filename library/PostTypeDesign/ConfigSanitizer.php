<?php

namespace Municipio\PostTypeDesign;

class ConfigSanitizer {
    private array $keys = [];

    public function __construct(private ?array $config) {}

    public function setKey(string $key):void
    {
        $this->keys[] = $key;
    }

    public function setKeys(array $keys):void
    {
        $this->keys = array_merge($keys, $this->keys);
    }

    public function transform():array
    {
        if (empty($this->config) || empty($this->keys)) {
            return $this->config ?? [];
        }

        foreach ($this->config as $key => $value) {
            if (!in_array($key, $this->keys)) {
                unset($this->config[$key]);
            }
        }

        $this->config = array_merge(array_fill_keys($this->keys, null), $this->config);

        return $this->config;
    }
}