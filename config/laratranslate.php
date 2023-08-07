<?php

return [
    /**
     * Number of spaces to prepend before key/translation pair when writing to PHP file
     */
    'number_of_spaces_to_preprend' => env('LARATRANSLATE_SPACES_TO_PREPREND', 4),

    /**
     * Language to take as default to compare to when translating files
     */
    'source_lang' => env('LARATRANSLATE_DEFAULT_SOURCE_LANG', config('app.locale')),

    'service_base_url' => 'https://laratranslate.jeanlucnguyen.com',
];
