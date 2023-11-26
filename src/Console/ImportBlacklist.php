<?php

namespace NiclasTimm\Blacklister\Console;

use Illuminate\Console\Command;
use NiclasTimm\Blacklister\Blacklister;

class ImportBlacklist extends Command
{
    protected $signature = 'blacklister:import {file}';

    protected $description = 'Import data into your blacklist form a csv file.';

    public function __construct(protected Blacklister $blacklister)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $file = $this->argument('file');

        if (!file_exists($file)) {
            $this->error('File '.$file.' not found');
            exit(1);
        }

        $blacklist = $this->blacklister->getBlacklistFromFile();

        $data = $this->readCsv($file);

        $withUpdatedDomains = $this->updateBlacklist('domains', $blacklist, $data);
        $withUpdatedEmails = $this->updateBlacklist('emails', $withUpdatedDomains, $data);

        $this->blacklister->overwriteBlacklistFile($withUpdatedEmails);
        $this->blacklister->invalidateCache();
        $this->blacklister->cacheBlacklist();
    }

    private function readCsv(string $file)
    {
        $rows = [];

        if (($h = fopen($file, "r")) !== false) {

            while (($data = fgetcsv($h, 1000, ",")) !== false) {
                $rows[] = $data;
            }

            fclose($h);
        }

        if (empty($rows)) {
            $this->error('Empty csv file');
            exit(1);
        }

        $final = [];
        $header = array_shift($rows);
        if (!$this->validateHeaders($header)) {
            $this->error('Invalid csv headers. Only "domains" and "emails" are allowed');
            exit(1);
        }

        foreach ($rows as $row) {
            $final[] = array_combine($header, $row);
        }

        return $final;
    }

    private function validateHeaders(array $headers): bool
    {
        return count($headers) === 2
            && in_array('emails', $headers)
            && in_array('domains', $headers);
    }

    private function updateBlacklist(string $type, array $currentBlacklist, array $csvData): array
    {
        $updatedBlacklist = $currentBlacklist;

        foreach ($csvData as $row) {
            foreach ($row as $header => $value) {
                if (in_array($value, $updatedBlacklist[$header])) {
                    continue;
                }
            }

            $updatedBlacklist[$type][] = $row[$type];
        }

        return $updatedBlacklist;
    }
}