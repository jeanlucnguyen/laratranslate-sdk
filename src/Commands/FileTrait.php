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

        return json_encode(eval($data), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
