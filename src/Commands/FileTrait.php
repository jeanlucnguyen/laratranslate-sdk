<?php

namespace Jeanlucnguyen\LaratranslateSdk\Commands;

trait FileTrait
{
    protected function getContentFromFileAsArray(string $path): array
    {
        $data = file_get_contents($path);

        if (! is_null(json_decode($data))) {
            return json_decode($data, true);
        }

        return eval($data);
    }

    protected function getContentFromFileAsJson(string $path): string
    {
        $data = file_get_contents($path);

        if (! is_null(json_decode($data))) {
            return $data;
        }

        $data = $this->readPhpFile($path);

        return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    protected function readPhpFile(string $path): array
    {
        return require $path;
    }
}
