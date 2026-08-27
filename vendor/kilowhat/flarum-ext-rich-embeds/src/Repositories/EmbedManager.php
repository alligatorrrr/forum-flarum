<?php

namespace Kilowhat\RichEmbeds\Repositories;

use Carbon\Carbon;
use Illuminate\Contracts\Queue\Queue;
use Kilowhat\RichEmbeds\Embed;
use Kilowhat\RichEmbeds\Jobs\ProcessUrl;

class EmbedManager
{
    protected $queue;

    public function __construct(Queue $queue)
    {
        $this->queue = $queue;
    }

    public function firstOrCreate(string $url, bool $forceReprocessing = false): Embed
    {
        // This code should not be used because long URLs will be stopped beforehand, but this is a failsafe
        if (mb_strlen($url) > Embed::$urlStringLength) {
            throw new \Exception('URL too long');
        }

        $hash = Embed::hashUrl($url);

        /**
         * @var Embed $embed
         */
        $embed = Embed::query()->where([
            'url_hash' => $hash,
        ])->first();

        if ($embed) {
            if ($forceReprocessing) {
                return $this->processAndReload($embed);
            }

            return $embed;
        }

        $embed = new Embed;
        $embed->url = $url;
        $embed->url_hash = $hash;
        $embed->created_at = Carbon::now();
        $embed->save();

        return $this->processAndReload($embed);
    }

    protected function processAndReload(Embed $embed): Embed
    {
        $this->queue->push(new ProcessUrl($embed));

        // Refresh model because even in sync queue the existing model won't be updated with new values
        return Embed::findOrFail($embed->id);
    }
}
