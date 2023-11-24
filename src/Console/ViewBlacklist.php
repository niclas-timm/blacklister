<?php

namespace NiclasTimm\Blacklister\Console;

use Illuminate\Console\Command;
use NiclasTimm\Blacklister\Blacklister;

class ViewBlacklist extends Command
{
    protected $signature = 'blacklister:view';

    protected $description = 'View your blacklist';

    public function __construct(protected Blacklister $blacklister)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $blacklist = $this->blacklister->getBlacklistFromFile();
        $this->info('Here is you blacklist:');

        foreach ($blacklist as $key => $values) {
            $this->line(strtoupper($key).':');

            if (empty($values)) {
                $this->line('-');
            } else {
                foreach ($values as $value) {
                    $this->line($value);
                }
            }

            $this->newLine();
        }
    }
}