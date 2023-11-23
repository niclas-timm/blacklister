<?php

namespace NiclasTimm\Blacklister;

class Validator
{
    protected Blacklister $blacklister;

    public function __construct(Blacklister $blacklister)
    {
        $this->blacklister = $blacklister;
    }

    public function validate($attribute, $value, $parameters): bool
    {
        $blacklist = $this->blacklister->getBlacklist();

        if (in_array($this->getDomain($value), $blacklist['domains'])) {
            return false;
        }

        return !in_array($value, $blacklist['emails']);
    }

    private function getDomain(string $value): string
    {
        return explode('@', $value)[1];
    }

    public function message($message, $attribute, $rule, $parameters)
    {
        return __(config('blacklister.validation_message'));
    }
}
