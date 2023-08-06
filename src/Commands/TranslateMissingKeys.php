<?php

namespace Jeanlucnguyen\LaratranslateSdk\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TranslateMissingKeys extends Command
{
    use GuessTrait;
    use FileTrait;

    public $signature = 'translate:translate-missing-keys
        {translated-file}: translation file in which to find missing keys
        {--source-lang=}: translation file language to compare to
        {--source-file=}: translation file to compare to';

    public $description = 'Translate keys missing in translated file';

    public function handle(): int
    {
        $sourceLang = $this->option('source-lang') ?: config('laratranslate.source_lang');

        $sourceFile = $this->option('source-file')
            ?: $this->guessSourceFile($this->argument('translated-file'), $sourceLang);

        $translatedFile = $this->argument('translated-file');

        $translatedLang = $this->guessLangFromPath($this->argument('translated-file'));

        $response = Http::acceptJson()
            ->attach(
                'original_file',
                $this->getContentFromFileAsJson(lang_path($sourceFile)),
                basename($sourceFile)
            )
            ->attach(
                'translated_file',
                $this->getContentFromFileAsJson(lang_path($translatedFile)),
                basename($translatedFile)
            )
            ->post(config('laratranslate.service_base_url').'/api/missing-keys/translate', [
                'original_lang' => $sourceLang,
                'translated_lang' => $translatedLang,
            ]);

        if ($response->failed()) {
            $this->error($response->body());

            return self::FAILURE;
        }

        $newTranslations = $response->json()['data'];

        $this->postCallHook($newTranslations, $translatedFile);

        return self::SUCCESS;
    }

    protected function postCallHook(array $newTranslations, string $translatedFile): void
    {
        $this->line(json_encode($newTranslations, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }
}
