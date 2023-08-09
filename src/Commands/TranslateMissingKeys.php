<?php

namespace Jeanlucnguyen\LaratranslateSdk\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Jeanlucnguyen\LaratranslateSdk\TranslationFile;
use RuntimeException;

class TranslateMissingKeys extends Command
{
    use GuessTrait;
    use FileTrait;

    protected const ALL_CHOICES = 'all';

    public $signature = 'translate:translate-missing-keys
        {translated-file?*}: translation file in which to find missing keys
        {--source-lang=}: translation file language to compare to
        {--source-file=}: translation file to compare to';

    public $description = 'Translate keys missing in translated file';

    public function handle(): int
    {
        $translatedFiles = $this->getTranslatedFilesToProcess();

        $sourceLang = $this->option('source-lang') ?: config('laratranslate.source_lang');

        foreach ($translatedFiles as $translatedFile) {
            $sourceFile = $this->option('source-file')
                ?: $this->guessSourceFile($translatedFile, $sourceLang);

            $translatedLang = $this->guessLangFromPath($translatedFile);

            $this->line('Processing file '.$translatedFile);

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

            $this->postCallHook($newTranslations, new TranslationFile($translatedFile));
            $this->info('');
        }

        return self::SUCCESS;
    }

    protected function postCallHook(array $newTranslations, TranslationFile $translatedFile): void
    {
        if (! $newTranslations) {
            $this->info('No new translations');
        } else {
            $encoded = json_encode($newTranslations, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

            if (! $encoded) {
                throw new RuntimeException('Unable to json_encode '.implode(',', $newTranslations));
            }

            $this->info($encoded);
        }
    }

    /**
     * @return array<int, string>
     */
    protected function getTranslatedFilesToProcess(): array
    {
        /** @var array<int, string> $translatedFileArgument */
        $translatedFileArgument = $this->argument('translated-file');

        if (! $translatedFileArgument) {
            $languagesChoice = $this->findAvailableLanguages();

            /** @var array<int, string> $languagesPicked */
            $languagesPicked = $this->choice(
                'For which language do you want to find missing keys?',
                Arr::prepend($languagesChoice, self::ALL_CHOICES),
                0,
                null,
                true
            );

            if (in_array(self::ALL_CHOICES, $languagesPicked)) {
                $languagesPicked = $languagesChoice;
            }

            foreach ($languagesPicked as $languagePicked) {
                $filesChoice[] = $this->findFilesInLanguage($languagePicked);
            }
            $filesChoice = Arr::flatten($filesChoice ?? []);

            /** @var array<int, string> $translatedFilesPicked */
            $translatedFilesPicked = $this->choice(
                'In which translation file do you want to find missing keys?',
                Arr::prepend($filesChoice, self::ALL_CHOICES),
                0,
                null,
                true
            );

            if (in_array(self::ALL_CHOICES, $translatedFilesPicked)) {
                return $filesChoice;
            }

            return $translatedFilesPicked;
        }

        return $translatedFileArgument;
    }
}
