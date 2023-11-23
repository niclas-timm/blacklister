<?php

namespace NiclasTimm\Blacklister\Console;

use Illuminate\Console\Command;
use NiclasTimm\Blacklister\Blacklister;

class VerifyBlacklisterSettings extends Command
{
    protected $signature = 'blacklister:verify';

    protected $description = 'Verify that the blacklister settings are correct';

    public function __construct(protected Blacklister $blacklister)
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->line('Start verification...');

        if (!$this->blacklister->blacklistExists()) {
            $this->error('The blacklister file does not exist or is not a valid json file. Please check your config in config/blacklister.php and consult the documentation.');

            return;
        }

        if (!$this->blacklister->hasValidStructure()) {
            $this->error('The blacklist json file does not have the required structure. Please check the documentation for more information.');

            return;
        }

        if (!$this->blacklister->hasValidValidationMessage()) {
            $this->error('You did not configure a valid validation message. Please configure one in config/blacklister.php');

            return;
        }

        if ($this->blacklister->isCacheEnabled()) {
            if (!$this->blacklister->hasValidCacheKey()) {
                $this->error('You did not configure a valid cache key. Please configure one in config/blacklister.php');

                return;
            }

            if (!$this->blacklister->hasValidCacheTtl()) {
                $this->error('You did not configure a valid cache TTL. Please configure one in config/blacklister.php');

                return;
            }
        }

        $this->info("Everything's fine. Go blacklist some stuff! 🎉");
    }
}