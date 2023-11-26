<?php

namespace NiclasTimm\Blacklister\Console;

use Illuminate\Console\Command;
use NiclasTimm\Blacklister\Blacklister;

class ExportBlackList extends Command
{
    protected $signature = 'blacklister:export {file}';

    protected $description = 'Export your blacklist as a csv file';

    public function __construct(protected Blacklister $blacklister)
    {
        parent::__construct();
    }

    public function handle()
    {
        $filename = $this->argument('file');

        if (file_exists($filename)) {
            if (!$this->confirm('File already exist. Overwrite?', 'no')) {
                $this->error('Aborted');
                exit(1);
            }

            unlink($filename);
        }

        $blacklist = $this->blacklister->getBlacklistFromFile();

        // Transform data.
        $rows = $this->transformBlacklist($blacklist);
        $file = fopen($filename, 'w');
        foreach ($rows as $row) {
            fputcsv($file, $row);
        }
        fclose($file);

        $this->info('Blacklist exported to '.$filename);
    }

    private function transformBlacklist(array $blacklist): array
    {
        $primaryKey = count($blacklist['emails']) > count($blacklist['domains'])
            ? 'emails'
            : 'domains';

        $secondaryKey = count($blacklist['emails']) > count($blacklist['domains'])
            ? 'domains'
            : 'emails';

        $res = [
            [$primaryKey, $secondaryKey],
        ];

        foreach ($blacklist[$primaryKey] as $index => $value) {
            $res[] = [$value, $blacklist[$secondaryKey][$index] ?? ''];
        }

        return $res;
    }
}