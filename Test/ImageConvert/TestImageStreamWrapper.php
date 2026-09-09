<?php

declare(strict_types=1);

namespace Municipio\Test\ImageConvert;

class TestImageStreamWrapper
{
    public static array $files = [];
    public static array $writtenPaths = [];
    private string $path = '';
    private int $position = 0;

    public static function reset(): void
    {
        self::$files = [];
        self::$writtenPaths = [];
    }

    public function stream_open(string $path, string $mode, int $options, ?string &$openedPath): bool
    {
        $parts          = parse_url($path);
        $this->path     = ltrim(($parts['host'] ?? '') . '/' . ltrim($parts['path'] ?? '', '/'), '/');
        $this->position = 0;

        if (str_contains($mode, 'w')) {
            self::$files[$this->path] = '';
            self::$writtenPaths[]     = $path;
        }

        return true;
    }

    public function stream_write(string $data): int
    {
        self::$files[$this->path] .= $data;
        $this->position          += strlen($data);

        return strlen($data);
    }

    public function stream_read(int $count): string
    {
        $data = substr(self::$files[$this->path] ?? '', $this->position, $count);
        $this->position += strlen($data);

        return $data;
    }

    public function stream_eof(): bool
    {
        return $this->position >= strlen(self::$files[$this->path] ?? '');
    }

    public function stream_stat(): array
    {
        return [];
    }
}