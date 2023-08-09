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

    /**
     * Commands scan the lang folder directory for available languages
     * List folders to exclude
     */
    'folders_to_exclude' => [
        'vendor',
    ],

    /**
     * Commands scan the lang folder directory for available languages
     * List files to exclude
     */
    'files_to_exclude' => [
        'sav-de.json',
        'sav-en.json',
        'sav-es.json',
        'sav-fr.json',
        'sav-it.json',
        'sav-pt.json',
    ],
];
