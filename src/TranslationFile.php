<?php

namespace Jeanlucnguyen\LaratranslateSdk;

class TranslationFile
{
    public function __construct(protected string $path)
    {
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function isPhp(): bool
    {
        return (bool) preg_match('/\.php$/', $this->path);
    }

    public function isJson(): bool
    {
        return (bool) preg_match('/\.json$/', $this->path);
    }

    public function basename(): string
    {
        return basename($this->path);
    }
}
