<?php

namespace Jeanlucnguyen\LaratranslateSdk\Commands;

class AddMissingKeys extends TranslateMissingKeys
{
    public $signature = 'translate:add-missing-keys
        {translated-file}: translation file in which to find missing keys
        {--source-lang=}: translation file language to compare to
        {--source-file=}: translation file to compare to
        {--sort}: sort translations by key in translated file';

    public $description = 'Add missing translations to translated file';

    protected function postCallHook(array $newTranslations, string $translatedFile): void
    {
        $this->addTranslationsToFile($newTranslations, lang_path($translatedFile));
    }

    protected function addTranslationsToFile(array $translations, string $filepath): void
    {
        $content = array_merge($this->getContentFromFileAsArray($filepath), $translations);

        if ($this->option('sort')) {
            ksort($content, SORT_NATURAL);
        }

        file_put_contents(
            $filepath,
            json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        );
    }
}
