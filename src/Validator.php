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
        if ($this->blacklister->hasBlockingCookie()) {
            return false;
        }

        $blacklist = $this->blacklister->getBlacklist();

        if (in_array($this->getDomain($value), $blacklist['domains'])) {
            return false;
        }

        $isBlacklisted = in_array($value, $blacklist['emails']);

        if (!$isBlacklisted) {
            return true;
        }

        $this->blacklister->setBlockingCookie();

        return false;
    }

    private function getDomain(string $value): string
    {
        $values = explode('@', $value);

        return $values[1] ?? $values[0];
    }

    public function message($message, $attribute, $rule, $parameters)
    {
        return __(config('blacklister.validation_message'));
    }
}
