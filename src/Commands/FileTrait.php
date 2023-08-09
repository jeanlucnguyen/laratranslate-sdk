<?php

namespace Jeanlucnguyen\LaratranslateSdk\Commands;

use RuntimeException;

trait FileTrait
{
    protected function getContentFromFileAsArray(string $path): array
    {
        $data = file_get_contents($path);

        if (! $data) {
            throw new RuntimeException('Unable to read file '.$path);
        }

        if (! is_null(json_decode($data))) {
            return json_decode($data, true);
        }

        return eval($data);
    }

    protected function getContentFromFileAsJson(string $path): string
    {
        $data = file_get_contents($path);

        if (! $data) {
            throw new RuntimeException('Unable to read file '.$path);
        }

        if (! is_null(json_decode($data))) {
            return $data;
        }

        $data = $this->readPhpFile($path);

        $encoded = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        if (! $encoded) {
            throw new RuntimeException('Unable to json_encode file');
        }

        return $encoded;
    }

    protected function readPhpFile(string $path): array
    {
        return require $path;
    }
}
