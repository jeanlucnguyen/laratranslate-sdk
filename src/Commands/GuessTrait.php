<?php

namespace Jeanlucnguyen\LaratranslateSdk\Commands;

use RuntimeException;

trait GuessTrait
{
    protected function jsonFileRegexPattern(): string
    {
        return '/^([a-zA-Z]{2})(_[a-zA-Z]{2})?(\.json)$/';
    }

    protected function phpFileRegexPattern(): string
    {
        return '/^([a-zA-Z]{2})(_[a-zA-Z]{2})?(\/.*\.php)$/';
    }

    protected function guessSourceFile(string $pathToGuessFrom, string $sourceLang): string
    {
        return $this->guessSourceFileFromJsonFile($pathToGuessFrom, $sourceLang)
            ?: $this->guessSourceFileFromPhpFile($pathToGuessFrom, $sourceLang)
            ?: throw new RuntimeException('Unable to find source file');
    }

    /**
     * Guess language from json translation file path (en.json)
     */
    protected function guessSourceFileFromJsonFile(string $pathToGuessFrom, string $sourceLang): ?string
    {
        $replaced = preg_replace(
            $this->jsonFileRegexPattern(),
            $sourceLang.'${3}',
            $pathToGuessFrom,
            -1,
            $count
        );

        if ($count > 0) {
            return $replaced;
        }

        return null;
    }

    /**
     * Guess language from php translation file path (en/auth.php)
     */
    protected function guessSourceFileFromPhpFile(string $pathToGuessFrom, string $sourceLang): ?string
    {
        $replaced = preg_replace(
            $this->phpFileRegexPattern(),
            $sourceLang.'${3}',
            $pathToGuessFrom,
            -1,
            $count
        );

        if ($count > 0) {
            return $replaced;
        }

        return null;
    }

    protected function guessLangFromPath(string $path): string
    {
        if (preg_match($this->jsonFileRegexPattern(), $path, $matches)) {
            return $matches[1];
        }

        if (preg_match($this->phpFileRegexPattern(), $path, $matches)) {
            return $matches[1];
        }

        throw new RuntimeException('Unable to guess language from file path '.$path);
    }
}
