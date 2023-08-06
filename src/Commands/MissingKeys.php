<?php

namespace Jeanlucnguyen\LaratranslateSdk\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class MissingKeys extends Command
{
    use GuessTrait;
    use FileTrait;

    public $signature = 'translate:missing-keys {translated-file} {--source-lang=}';

    public $description = 'Find keys missing in translated file';

    public function handle(): int
    {
        $sourceLang = $this->option('source-lang') ?: config('laratranslate.source_lang');

        $sourceFile = $this->guessSourceFile($this->argument('translated-file'), $sourceLang);

        $translatedFile = $this->argument('translated-file');

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
            ->post(config('laratranslate.service_base_url').'/api/missing-keys');

        if ($response->failed()) {
            $this->error($response->body());

            return self::FAILURE;
        }

        $this->line($response->json()['data']);

        return self::SUCCESS;
    }
}
