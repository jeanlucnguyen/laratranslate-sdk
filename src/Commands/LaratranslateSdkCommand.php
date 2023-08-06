<?php

namespace Jeanlucnguyen\LaratranslateSdk\Commands;

use Illuminate\Console\Command;

class LaratranslateSdkCommand extends Command
{
    public $signature = 'laratranslate-sdk';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
