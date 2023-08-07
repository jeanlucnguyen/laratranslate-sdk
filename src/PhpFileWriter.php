<?php

namespace Jeanlucnguyen\LaratranslateSdk;

class PhpFileWriter
{
    public function append(array $content, string $filepath): bool
    {
        $lineToInsertContent = $this->findLineToInsertContent($filepath);

        $fn = fopen($filepath, 'r');
        $currentLine = 1;
        do {
            $newContent[] = fgets($fn);

            if ($currentLine === $lineToInsertContent) {
                foreach ($content as $key => $translation) {
                    $newContent[] = $this->addTranslation($key, $translation);
                }
            }

            $currentLine++;
        } while (! feof($fn));

        fclose($fn);

        file_put_contents($filepath, $newContent);

        return true;
    }

    protected function findLineToInsertContent(string $filepath): int
    {
        $tokens = token_get_all(file_get_contents($filepath));

        $tokens = array_reverse($tokens);

        foreach ($tokens as $token) {
            if ($token === ';') {
                $closingSemiColon = true;
            }

            if (is_array($token) && ($closingSemiColon ?? false)) {
                return $token[2];
            }
        }

        return 1;
    }

    protected function addTranslation(string $key, string $translation): string
    {
        return str_repeat(' ', config('laratranslate.number_of_spaces_to_preprend'))
            ."'".$key."' => '".addslashes($translation)."',".PHP_EOL;
    }
}
