<?php

namespace Jeanlucnguyen\LaratranslateSdk\Commands;

use Jeanlucnguyen\LaratranslateSdk\JsonFileWriter;
use Jeanlucnguyen\LaratranslateSdk\PhpFileWriter;
use Jeanlucnguyen\LaratranslateSdk\TranslationFile;
use RuntimeException;

class AddMissingKeys extends TranslateMissingKeys
{
    public $signature = 'translate:add-missing-keys
        {translated-file?*}: translation file in which to find missing keys
        {--source-lang=}: translation file language to compare to
        {--source-file=}: translation file to compare to
        {--sort}: sort translations by key in translated file';

    public $description = 'Add missing translations to translated file';

    protected function postCallHook(array $newTranslations, TranslationFile $translatedFile): void
    {
        $this->addTranslationsToFile($newTranslations, $translatedFile);
    }

    protected function addTranslationsToFile(array $translations, TranslationFile $translationFile): bool
    {
        if ($translationFile->isJson()) {
            return $this->addTranslationsToJsonFile($translations, $translationFile);
        } elseif ($translationFile->isPhp()) {
            return $this->addTranslationsToPhpFile($translations, $translationFile);
        }

        throw new RuntimeException('Translation file format unknown');
    }

    protected function addTranslationsToJsonFile(array $translations, TranslationFile $translationFile): bool
    {
        $content = array_merge($this->getContentFromFileAsArray(lang_path($translationFile->getPath())), $translations);

        if ($this->option('sort')) {
            ksort($content, SORT_NATURAL);
        }

        return (new JsonFileWriter())->write($content, lang_path($translationFile->getPath()));
    }

    protected function addTranslationsToPhpFile(array $translations, TranslationFile $translationFile): bool
    {
        return (new PhpFileWriter())->append($translations, lang_path($translationFile->getPath()));
    }
}
