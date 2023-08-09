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

    /**
     * Guess source file from translation file path
     * en.json => fr.json
     * en/auth.php => fr/auth.php
     */
    protected function guessSourceFile(string $pathToGuessFrom, string $sourceLang): string
    {
        return $this->guessSourceFileFromJsonFile($pathToGuessFrom, $sourceLang)
            ?: $this->guessSourceFileFromPhpFile($pathToGuessFrom, $sourceLang)
            ?: throw new RuntimeException('Unable to find source file');
    }

    /**
     * Guess source file from json translation file path
     * en.json => fr.json
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
     * Guess source file from php translation file path
     * en/auth.php => fr/auth.php
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

    /**
     * Guess ISO language code with or without ISO country code (example: en, en_US) from file path
     * en/auth.php => en
     */
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

    /**
     * Return available languages from lang_path()
     * ['fr', 'en', 'it']
     */
    protected function findAvailableLanguages(): array
    {
        $files = scandir(lang_path());

        if ($files === false) {
            throw new RuntimeException('Unable to read directory '.lang_path());
        }

        foreach ($files as $file) {
            if (is_dir(lang_path($file)) && ! $this->folderIsExcluded($file)) {
                $languages[] = $file;
            } elseif (preg_match('/\.json$/', $file) && ! $this->fileIsExcluded($file)) {
                $languages[] = preg_replace('/(.*)\.json$/', '${1}', $file);
            }
        }

        return array_values(array_unique($languages ?? []));
    }

    /**
     * Return all files available for a language
     * ['fr.json', 'fr/auth.php', 'fr/validation.php', ...]
     */
    protected function findFilesInLanguage(string $language): array
    {
        if (file_exists(lang_path($language.'.json'))) {
            $languageFiles[] = $language.'.json';
        }

        $files = scandir(lang_path($language));

        if ($files === false) {
            throw new RuntimeException('Unable to read directory '.lang_path($language));
        }

        foreach ($files as $file) {
            if (! is_dir($file) && ! $this->fileIsExcluded($language.'/'.$file)) {
                $languageFiles[] = $language.'/'.$file;
            }
        }

        return $languageFiles ?? [];
    }

    protected function folderIsExcluded(string $folderPath): bool
    {
        return in_array(
            $folderPath,
            array_merge(config('laratranslate.folders_to_exclude'), ['.', '..'])
        );
    }

    protected function fileIsExcluded(string $filePath): bool
    {
        return in_array($filePath, config('laratranslate.files_to_exclude'));
    }
}
