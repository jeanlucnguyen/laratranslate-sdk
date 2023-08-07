<?php

namespace Jeanlucnguyen\LaratranslateSdk;

class JsonFileWriter
{
    public function write(array $content, string $filepath): bool
    {
        return (bool) file_put_contents(
            $filepath,
            json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        );
    }
}
