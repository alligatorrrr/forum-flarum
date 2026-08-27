<?php

namespace Kilowhat\RichEmbeds\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Contracts\Queue\Queue;
use Illuminate\Database\Eloquent\Builder;
use Kilowhat\RichEmbeds\Embed;
use Kilowhat\RichEmbeds\Jobs\ProcessUrl;

class RefreshCommand extends Command
{
    protected $signature = 'kilowhat:rich-embeds:refresh ' .
    '{pattern? : A complete URL to match against. Can contain * match-all pattern} ' .
    '{--api-resource= : A filter to only select embeds with an existing API-powered embed. Comma separated. Valid values: flarum.*,flarum.discussion,flarum.user,github.*,github.repo,github.issue,github.pull,youtube.video,googledrive.file} ' .
    '{--older-than=1 day ago : A filter to skip recently updated embeds. Defaults to 1 day. Can be any text expression supported by Carbon} ' .
    '{--any-age : Shortcut to disable the older-than parameter} ' .
    '{--failed : Retry previously failed embeds} ' .
    '{--dry-run : Output all information but don\'t persist anything or perform any request}';
    protected $description = 'Refresh existing embeds matching the given conditions. ' .
    'This may cause a lot of external queries in quick succession. ' .
    'If API-powered embeds are enabled, this can incur costs. ' .
    'Run with --dry-run first to find out how many embeds will be affected.';

    protected $queue;

    public function __construct(Queue $queue)
    {
        $this->queue = $queue;

        parent::__construct();
    }

    public function handle()
    {
        if ($this->option('dry-run')) {
            $this->warn('Dry mode enabled. Nothing will be persisted and no HTML or API request will be performed');
        }

        $query = Embed::query();

        if (!$this->option('any-age')) {
            $query->where('retrieved_at', '<', Carbon::parse($this->option('older-than')));
        }

        if (!$this->option('failed')) {
            $query->where(function (Builder $query) {
                $query->whereNull('error')
                    ->orWhereNotNull('api_resource');
            });
        }

        if ($pattern = $this->argument('pattern')) {
            $query->where('url', 'like', str_replace('*', '%', $pattern));
        }

        if ($api = $this->option('api-resource')) {
            $api = str_replace('flarum.*', 'flarum.discussion,flarum.user', $api);
            $api = str_replace('github.*', 'github.repo,github.issue,github.pull', $api);

            $apiPaths = explode(',', $api);

            foreach ($apiPaths as $path) {
                if (!in_array($path, [
                    'flarum.discussion',
                    'flarum.user',
                    'github.repo',
                    'github.issue',
                    'github.pull',
                    'youtube.video',
                    'googledrive.file',
                ])) {
                    $this->error("Invalid --api-resource argument $path. Aborting");

                    return;
                }
            }

            $query->whereRaw("JSON_CONTAINS_PATH(api_resource, 'one'" . implode('', array_map(function ($path) {
                    // Use double-quotes around JSON path to escape the dots
                    // No additional escaping is needed since we whitelist all possible options above
                    return ', \'$."' . $path . '"\'';
                }, $apiPaths)) . ')');
        }

        $this->output->progressStart($query->count());

        $query->each(function (Embed $embed) {
            if (!$this->option('dry-run')) {
                $this->queue->push(new ProcessUrl($embed));
            }

            $this->output->progressAdvance();
        });

        $this->output->progressFinish();
    }
}
