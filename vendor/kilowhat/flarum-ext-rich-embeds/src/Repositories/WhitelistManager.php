<?php

namespace Kilowhat\RichEmbeds\Repositories;

use Flarum\Settings\SettingsRepositoryInterface;
use Kilowhat\RichEmbeds\Embed;
use Kilowhat\RichEmbeds\Exceptions\BlacklistedUrl;
use Kilowhat\RichEmbeds\Exceptions\InvalidUrl;
use Laminas\Diactoros\Uri;

class WhitelistManager
{
    protected $settings;

    public function __construct(SettingsRepositoryInterface $settings)
    {
        $this->settings = $settings;
    }

    public function isWhitelisted(string $url): bool
    {
        if (mb_strlen($url) > Embed::$urlStringLength) {
            return false;
        }

        // Extract host here for performance
        $host = strtolower((new Uri($url))->getHost());

        $blacklist = trim($this->settings->get('kilowhat-rich-embeds.blacklist'));

        foreach (explode("\n", $blacklist) as $line) {
            $expression = trim($line);

            // Do not evaluate empty lines
            if (!$expression) {
                continue;
            }

            if ($this->check($url, $host, $expression)) {
                return false;
            }
        }

        $whitelist = trim($this->settings->get('kilowhat-rich-embeds.whitelist'));

        foreach (explode("\n", $whitelist) as $line) {
            $expression = trim($line);

            // Do not evaluate empty lines
            if (!$expression) {
                continue;
            }

            if ($this->check($url, $host, $expression)) {
                return true;
            }
        }

        // If there was a whitelist, deny everything else
        if ($whitelist) {
            return false;
        }

        // If there was only a blacklist or neither blacklist+whitelist,
        // we'll throw in another check for local network addresses
        $isIP = filter_var(
            $host,
            FILTER_VALIDATE_IP
        );
        $isPublicIP = filter_var(
            $host,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        );

        if ($isIP && !$isPublicIP) {
            return false;
        }

        return true;
    }

    protected function check(string $url, string $host, string $expression): bool
    {
        if (strlen($expression) > 0 && ($expression[0] === '/' || $expression[0] === '~')) {
            return preg_match($expression, $url) === 1;
        } else {
            // Check domain ends with expression
            return preg_match('~(\.|^)' . preg_quote($expression, '~') . '$~', $host) === 1;
        }
    }

    public function assertWhitelisted(string $url): void
    {
        if (!$this->isWhitelisted($url)) {
            throw new BlacklistedUrl($url);
        }
    }

    public function assertValidUrl(string $url): void
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidUrl($url);
        }
    }
}
