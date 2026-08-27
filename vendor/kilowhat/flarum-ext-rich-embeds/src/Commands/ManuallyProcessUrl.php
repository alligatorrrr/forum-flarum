<?php

namespace Kilowhat\RichEmbeds\Commands;

use Illuminate\Console\Command;
use Kilowhat\RichEmbeds\Embed;
use Kilowhat\RichEmbeds\Exceptions\InvalidUrl;
use Kilowhat\RichEmbeds\Repositories\EmbedManager;
use Kilowhat\RichEmbeds\Repositories\WhitelistManager;
use Symfony\Component\Console\Output\OutputInterface;

class ManuallyProcessUrl extends Command
{
    protected $signature = 'kilowhat:rich-embeds:process {url : A fully qualified URL}';
    protected $description = 'Create or refresh a single embed by URL. This command will bypass the whitelist/blacklist settings and is intended for troubleshooting.';

    protected $manager;
    protected $whitelist;

    public function __construct(EmbedManager $manager, WhitelistManager $whitelist)
    {
        $this->manager = $manager;
        $this->whitelist = $whitelist;

        parent::__construct();
    }

    public function handle()
    {
        $rawUrl = $this->argument('url');

        try {
            $this->whitelist->assertValidUrl($rawUrl);
        } catch (InvalidUrl $exception) {
            $this->error('Invalid URL format');
            return;
        }

        $url = Embed::normalizeUrl($rawUrl);

        if (mb_strlen($url) > Embed::$urlStringLength) {
            $this->error('URLs longer than ' . Embed::$urlStringLength . ' bytes cannot be processed');
            return;
        }

        // Warn but do not prevent processing non whitelisted URLs via the command line
        // This could be necessary to refresh existing embeds
        if (!$this->whitelist->isWhitelisted($url)) {
            $this->warn('This URL would normally be blocked by the whitelist');
        }

        $embed = $this->manager->firstOrCreate($url, true);

        if (!$embed->retrieved_at) {
            $this->info('The process job has been dispatched but no results are available yet. It probably means you have an asynchronous queue set up');
            return;
        }

        if ($embed->error) {
            $this->warn('Error: ' . $embed->error);
        }

        $this->info('Retrieved: ' . $embed->retrieved_at->format(DATE_W3C));
        $this->info('HTTP Status: ' . $embed->http_status);

        if ($this->getOutput()->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $this->info('OpenGraph: ' . json_encode($embed->opengraph, JSON_PRETTY_PRINT));
            $this->info('Icons: ' . json_encode($embed->icons, JSON_PRETTY_PRINT));
            $this->info('Fallback: ' . json_encode($embed->fallback, JSON_PRETTY_PRINT));
            $this->info('API Resource: ' . json_encode($embed->api_resource, JSON_PRETTY_PRINT));
        }
    }
}
