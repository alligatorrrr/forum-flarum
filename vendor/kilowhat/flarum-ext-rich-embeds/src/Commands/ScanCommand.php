<?php

namespace Kilowhat\RichEmbeds\Commands;

use Flarum\Post\CommentPost;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Kilowhat\RichEmbeds\Embed;
use Kilowhat\RichEmbeds\Repositories\EmbedManager;
use Kilowhat\RichEmbeds\Repositories\WhitelistManager;
use s9e\TextFormatter\Utils;
use Symfony\Component\Console\Output\OutputInterface;

class ScanCommand extends Command
{
    protected $signature = 'kilowhat:rich-embeds:scan {--dry-run}';
    protected $description = 'Scan all posts in the database to create missing embeds and remove embeds no longer allowed. ' .
    'Posts are not re-parsed, so if a link is already replaced by another extension (like MediaEmbed) it won\'t be switched to a Rich Embed link. ' .
    'This may cause a lot of external queries in quick succession. ' .
    'If API-powered embeds are enabled, this can incur costs. ' .
    'Run with -v to show post-by-post statistics. ' .
    'Run with --dry-run first to find out how many embeds will be affected.';

    protected $manager;
    protected $whitelist;
    protected $settings;

    public function __construct(EmbedManager $manager, WhitelistManager $whitelist, SettingsRepositoryInterface $settings)
    {
        $this->manager = $manager;
        $this->whitelist = $whitelist;
        $this->settings = $settings;

        parent::__construct();
    }

    public function handle()
    {
        if ($this->option('dry-run')) {
            $this->warn('Dry mode enabled. Nothing will be persisted and no HTML or API request will be performed');
        }

        $query = CommentPost::query()->where('type', CommentPost::$type)->orderBy('id');

        $showDetails = $this->getOutput()->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE;

        if (!$showDetails) {
            $this->output->progressStart($query->count());
        }

        $totalImageCount = 0;
        $totalBbcodeCount = 0;
        $totalLinkCount = 0;

        // The logic is very similar to SavePost but it must be duplicated for the dry-run behaviour and statistics
        $query->with('user')->each(function (CommentPost $post) use ($showDetails, &$totalImageCount, &$totalBbcodeCount, &$totalLinkCount) {
            // TODO: permissions can't be eager-loaded
            $allowedToUseLinkEmbeds = $post->user && $post->user->hasPermission('kilowhat-rich-embeds.useOnOwnPost');

            $embedIds = [];
            $imageCount = 0;
            $bbcodeCount = 0;
            $linkCount = 0;

            if ($this->settings->get('kilowhat-rich-embeds.imageEmbeds')) {
                // Do IMG first, that way the code below will replace false with true for is_link attribute without the need for any if block
                Utils::replaceAttributes($post->getParsedContentAttribute(), 'IMG', function ($attributes) use (&$embedIds, &$imageCount) {
                    $url = Embed::normalizeUrl(Arr::get($attributes, 'src'));

                    if ($url && $this->whitelist->isWhitelisted($url)) {
                        if (!$this->option('dry-run')) {
                            $embedIds[$this->manager->firstOrCreate($url)->id] = false;
                        }

                        $imageCount++;
                    }

                    // The return value is not important, we just use the Util function as a helper to loop through the tags
                    return [];
                });
            }

            if ($allowedToUseLinkEmbeds && !$post->kilowhat_rich_embeds_disable) {
                Utils::replaceAttributes($post->getParsedContentAttribute(), 'RICH-URL', function ($attributes) use (&$embedIds, &$bbcodeCount) {
                    $url = Embed::normalizeUrl(Arr::get($attributes, 'rich-url'));

                    if ($url && $this->whitelist->isWhitelisted($url)) {
                        if (!$this->option('dry-run')) {
                            $embedIds[$this->manager->firstOrCreate($url)->id] = true;
                        }

                        $bbcodeCount++;
                    }

                    return [];
                });

                Utils::replaceAttributes($post->getParsedContentAttribute(), 'URL', function ($attributes) use (&$embedIds, &$linkCount) {
                    $url = Embed::normalizeUrl(Arr::get($attributes, 'url'));

                    if ($url && $this->whitelist->isWhitelisted($url)) {
                        if (!$this->option('dry-run')) {
                            $embedIds[$this->manager->firstOrCreate($url)->id] = true;
                        }

                        $linkCount++;
                    }

                    return [];
                });
            }

            if (!$this->option('dry-run')) {
                $post->kilowhatRichEmbeds()->sync(array_map(function ($isLink) {
                    return [
                        'is_link' => $isLink,
                    ];
                }, $embedIds));
            }

            $totalImageCount += $imageCount;
            $totalBbcodeCount += $bbcodeCount;
            $totalLinkCount += $linkCount;

            if ($showDetails) {
                $this->info(
                    'Post #' . $post->id .
                    ' - Embeds ' . ($allowedToUseLinkEmbeds ? 'allowed' : 'disallowed') .
                    ($post->kilowhat_rich_embeds_disable ? ' - Manually disabled' : '') .
                    " - Images $imageCount" .
                    " - BBcodes $bbcodeCount" .
                    " - Links $linkCount"
                );
            } else {
                $this->output->progressAdvance();
            }
        });

        if (!$showDetails) {
            $this->output->progressFinish();
        }

        $this->info("Total images scanned $totalImageCount");
        $this->info("Total bbcode scanned $totalBbcodeCount");
        $this->info("Total links scanned $totalLinkCount");
    }
}
