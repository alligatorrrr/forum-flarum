<?php

namespace Kilowhat\RichEmbeds;

use Carbon\Carbon;
use Flarum\Api\Serializer\AbstractSerializer;
use Flarum\Http\UrlGenerator;
use Flarum\Locale\Translator;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Support\Arr;
use Laminas\Diactoros\Uri;

class EmbedSerializer extends AbstractSerializer
{
    protected $type = 'kilowhat-rich-embeds';

    /**
     * @param Embed $embed
     * @return string
     */
    public function getId($embed): string
    {
        return $embed->url;
    }

    /**
     * @param Embed $embed
     * @return array
     */
    protected function getDefaultAttributes($embed): array
    {
        $state = 'retrieving';

        if ($embed->retrieved_at) {
            if ($embed->error && !$embed->api_resource) {
                $state = 'error';
            } else {
                $state = 'ready';
            }
        }

        return [
            'finalUrl' => $embed->final_url ?? $embed->url, // Fallback for embeds from earlier versions
            'mime' => $embed->mime,
            'opengraph' => $embed->opengraph,
            'icons' => $embed->icons,
            'fallback' => $embed->fallback,
            'exif' => $embed->exif,
            'width' => $embed->width,
            'height' => $embed->height,
            'size' => $embed->size,
            'domain' => preg_replace('~^www\.~', '', (new Uri($embed->final_url ?? $embed->url))->getHost()),
            'state' => $state,
            'preview' => $this->preview($embed),
            'canRefresh' => $this->actor->hasPermission('kilowhat-rich-embeds.refreshOnAnyPost'),
        ];
    }

    protected function preview(Embed $embed): ?array
    {
        $api = $embed->api_resource ?: [];
        $opengraph = $embed->opengraph ?: [];
        $domain = preg_replace('~^www\.~', '', (new Uri($embed->final_url ?? $embed->url))->getHost());

        $fallbackSiteName = Arr::get($opengraph, 'site_name') ?: $domain;

        $opengraphImages = array_map(function ($image) use ($fallbackSiteName) {
            return [
                'src' => Arr::get($image, 'secure_url') ?: Arr::get($image, 'url'),
                'alt' => Arr::get($image, 'alt') ?: $fallbackSiteName,
            ];
        }, Arr::get($opengraph, 'images') ?: []);

        $settings = resolve(SettingsRepositoryInterface::class);

        $translator = resolve(Translator::class);

        if (
            Arr::exists($api, 'flarum.discussion') &&
            ($title = Arr::get($api['flarum.discussion'], 'data.attributes.title')) &&
            $settings->get('kilowhat-rich-embeds.flarumApiPreviews')
        ) {
            $stats = [];

            $commentCount = Arr::get($api['flarum.discussion'], 'data.attributes.commentCount');

            if (is_numeric($commentCount)) {
                $stats[] = [
                    'icon' => 'far fa-comments',
                    'label' => $translator->trans('kilowhat-rich-embeds.api.embed.flarum.discussion.stats.commentCount'),
                    'value' => $commentCount,
                    'number' => true,
                ];
            }

            $participantCount = Arr::get($api['flarum.discussion'], 'data.attributes.participantCount');

            if (is_numeric($participantCount)) {
                $stats[] = [
                    'icon' => 'fas fa-users',
                    'label' => $translator->trans('kilowhat-rich-embeds.api.embed.flarum.discussion.stats.participantCount'),
                    'value' => $participantCount,
                    'number' => true,
                ];
            }

            if ($createdAt = Arr::get($api['flarum.discussion'], 'data.attributes.createdAt')) {
                $stats[] = [
                    'icon' => 'far fa-calendar',
                    'label' => $translator->trans('kilowhat-rich-embeds.api.embed.flarum.discussion.stats.createdAt'),
                    'value' => $createdAt,
                    'date' => true,
                ];
            }

            $preview = [
                'siteName' => $fallbackSiteName,
                'contentType' => $translator->trans('kilowhat-rich-embeds.api.embed.flarum.discussion.type'),
                'title' => $title,
                'images' => $this->uniqueAndLimitImages($opengraphImages, $settings),
                'stats' => $stats,
            ];

            if ($userId = Arr::get($api['flarum.discussion'], 'data.relationships.user.data.id')) {
                $user = Arr::first(Arr::get($api['flarum.discussion'], 'included') ?: [], function ($resource) use ($userId) {
                    return Arr::get($resource, 'type') === 'users' && Arr::get($resource, 'id') === $userId;
                });

                if ($user && $displayName = Arr::get($user, 'attributes.displayName')) {
                    $preview['author'] = [
                        'avatarUrl' => Arr::get($user, 'attributes.avatarUrl'),
                        'username' => $displayName,
                    ];
                }
            }

            $discussionId = Arr::get($api['flarum.discussion'], 'data.id');

            // Unfortunately the firstPost relationship isn't included in the payload by default,
            // so we'll have to fallback to using post with number 1 as the first post
            // Another idea could be to use the post that's permalinked in the URL for the preview but this requires assuming the routing hasn't been altered
            $firstPost = Arr::first(Arr::get($api['flarum.discussion'], 'included') ?: [], function ($resource) use ($discussionId) {
                return Arr::get($resource, 'type') === 'posts' &&
                    Arr::get($resource, 'attributes.number') === 1 &&
                    Arr::get($resource, 'relationships.discussion.data.id') === $discussionId;
            });

            if ($firstPost && $content = Arr::get($firstPost, 'attributes.contentHtml')) {
                // Stripping tags is done purely for readability. We are not going to insert this as HTML in any situation
                $preview['description'] = strip_tags($content);

                // We will pick fallback images from the post, but only if the forum has no opengraph feature that already made some choices
                if (count($opengraphImages) === 0) {
                    $postImages = [];

                    preg_replace_callback('~<img[^>]+src="([^"]+)"[^>]+>~', function ($matches) use (&$postImages) {
                        $postImages[] = [
                            'src' => $matches[1],
                            'alt' => 'Image from post',
                        ];
                    }, $content);

                    if (count($postImages)) {
                        $preview['images'] = $this->uniqueAndLimitImages($postImages, $settings);
                    }
                }
            }

            return $this->finishFlarumPreview($preview, $api);
        }

        if (
            Arr::exists($api, 'flarum.user') &&
            ($displayName = Arr::get($api['flarum.user'], 'data.attributes.displayName')) &&
            $settings->get('kilowhat-rich-embeds.flarumApiPreviews')
        ) {
            $stats = [];

            $discussionCount = Arr::get($api['flarum.user'], 'data.attributes.discussionCount');

            if (is_numeric($discussionCount)) {
                $stats[] = [
                    'icon' => 'far fa-comments',
                    'label' => $translator->trans('kilowhat-rich-embeds.api.embed.flarum.user.stats.discussionCount'),
                    'value' => $discussionCount,
                    'number' => true,
                ];
            }

            $commentCount = Arr::get($api['flarum.user'], 'data.attributes.commentCount');

            if (is_numeric($commentCount)) {
                $stats[] = [
                    'icon' => 'fas fa-reply-all',
                    'label' => $translator->trans('kilowhat-rich-embeds.api.embed.flarum.user.stats.commentCount'),
                    'value' => $commentCount,
                    'number' => true,
                ];
            }

            if ($joinTime = Arr::get($api['flarum.user'], 'data.attributes.joinTime')) {
                $stats[] = [
                    'icon' => 'far fa-calendar',
                    'label' => $translator->trans('kilowhat-rich-embeds.api.embed.flarum.user.stats.joinTime'),
                    'value' => $joinTime,
                    'date' => true,
                ];
            }

            $preview = [
                'siteName' => $fallbackSiteName,
                'contentType' => $translator->trans('kilowhat-rich-embeds.api.embed.flarum.user.type'),
                'title' => $displayName,
                'description' => Arr::get($api['flarum.user'], 'data.attributes.bio'),
                'stats' => $stats,
            ];

            if ($avatarUrl = Arr::get($api['flarum.user'], 'data.attributes.avatarUrl')) {
                $preview['images'] = [
                    [
                        'src' => $avatarUrl,
                        'alt' => 'Avatar',
                    ],
                ];
            }

            return $this->finishFlarumPreview($preview, $api);
        }

        if (
            (Arr::exists($api, 'github.issue') || Arr::exists($api, 'github.pull')) &&
            $settings->get('kilowhat-rich-embeds.githubApiKey')
        ) {
            $issueOrPull = Arr::exists($api, 'github.issue') ? $api['github.issue'] : $api['github.pull'];
            $translationPrefix = Arr::exists($api, 'github.issue') ? 'issue' : 'pull';

            $number = Arr::get($issueOrPull, 'number');

            $stats = [];

            $commentCount = Arr::get($issueOrPull, 'comments');

            if (is_numeric($commentCount)) {
                $stats[] = [
                    'icon' => 'far fa-comments',
                    'label' => $translator->trans("kilowhat-rich-embeds.api.embed.github.$translationPrefix.stats.commentCount"),
                    'value' => $commentCount,
                    'number' => true,
                ];
            }

            // Will only apply to PRs
            $reviewComments = Arr::get($issueOrPull, 'review_comments');

            if (is_numeric($reviewComments)) {
                $stats[] = [
                    'icon' => 'far fa-comment-dots',
                    'label' => $translator->trans('kilowhat-rich-embeds.api.embed.github.pull.stats.reviewCount'),
                    'value' => $reviewComments,
                    'number' => true,
                ];
            }

            // Will only apply to PRs
            $commitCount = Arr::get($issueOrPull, 'commits');

            if (is_numeric($commitCount)) {
                $stats[] = [
                    'icon' => 'fas fa-terminal',
                    'label' => $translator->trans('kilowhat-rich-embeds.api.embed.github.pull.stats.commitCount'),
                    'value' => $commitCount,
                    'number' => true,
                ];
            }

            if ($createdAt = Arr::get($issueOrPull, 'created_at')) {
                $stats[] = [
                    'icon' => 'far fa-calendar',
                    'label' => $translator->trans("kilowhat-rich-embeds.api.embed.github.$translationPrefix.stats.createdAt"),
                    'value' => $createdAt,
                    'date' => true,
                ];
            }

            $authorLogin = Arr::get($issueOrPull, 'user.login');

            $repo = Arr::get($issueOrPull, 'base.repo') ?: (Arr::exists($api, 'github.repo') ? $api['github.repo'] : []);

            $title = ($number ? '#' . $number . ' ' : '') . Arr::get($issueOrPull, 'title');

            return [
                'icon' => 'https://github.githubassets.com/favicons/favicon.svg',
                'siteName' => (Arr::get($repo, 'full_name', '') . (Arr::get($repo, 'private') ? ' ' . $translator->trans('kilowhat-rich-embeds.api.embed.github.privateRepoSuffix') : '')) ?: $fallbackSiteName,
                'contentType' => $translator->trans("kilowhat-rich-embeds.api.embed.github.$translationPrefix.type"),
                'title' => $title,
                'inlineTitle' => '[' . (Arr::get($repo, 'full_name') ?: $fallbackSiteName) . '] ' . $title,
                // Remove HTML-style comments from the body
                // It's common, at least in Flarum repos, to have a very long comment at the start of the issue which doesn't get deleted
                'description' => preg_replace('~<!--[\s\S]*?-->~', '', Arr::get($issueOrPull, 'body') ?: ''),
                'images' => $this->uniqueAndLimitImages($opengraphImages, $settings),
                'stats' => $stats,
                'author' => $authorLogin ? [
                    'avatarUrl' => Arr::get($issueOrPull, 'user.avatar_url'),
                    'username' => $authorLogin,
                    'url' => Arr::get($issueOrPull, 'user.html_url'),
                ] : null,
            ];
        }

        if (
            Arr::exists($api, 'github.repo') &&
            $settings->get('kilowhat-rich-embeds.githubApiKey')
        ) {
            $repoUrl = 'https://github.com/' . Arr::get($api['github.repo'], 'full_name');

            return [
                'icon' => 'https://github.githubassets.com/favicons/favicon.svg',
                'siteName' => $translator->trans('kilowhat-rich-embeds.api.embed.github.siteName'),
                'contentType' => $translator->trans('kilowhat-rich-embeds.api.embed.github.repo.type.' . (Arr::get($api['github.repo'], 'private') ? 'private' : 'public')),
                'title' => Arr::get($api['github.repo'], 'full_name'),
                'images' => $this->uniqueAndLimitImages($opengraphImages, $settings),
                'stats' => [
                    [
                        'icon' => 'far fa-dot-circle',
                        'label' => $translator->trans('kilowhat-rich-embeds.api.embed.github.repo.stats.openIssueCount'),
                        'value' => Arr::get($api['github.repo'], 'open_issues_count') ?: 0,
                        'number' => true,
                        'url' => $repoUrl . '/issues',
                    ],
                    [
                        'icon' => 'far fa-eye',
                        'label' => $translator->trans('kilowhat-rich-embeds.api.embed.github.repo.stats.subscriberCount'),
                        'value' => Arr::get($api['github.repo'], 'subscribers_count') ?: 0,
                        'number' => true,
                        'url' => $repoUrl . '/watchers',
                    ],
                    [
                        'icon' => 'far fa-star',
                        'label' => $translator->trans('kilowhat-rich-embeds.api.embed.github.repo.stats.stargazerCount'),
                        'value' => Arr::get($api['github.repo'], 'stargazers_count') ?: 0,
                        'number' => true,
                        'url' => $repoUrl . '/stargazers',
                    ],
                    [
                        'icon' => 'fas fa-code-branch',
                        'label' => $translator->trans('kilowhat-rich-embeds.api.embed.github.repo.stats.forkCount'),
                        'value' => Arr::get($api['github.repo'], 'forks_count') ?: 0,
                        'number' => true,
                        'url' => $repoUrl . '/network/members',
                    ],
                ],
            ];
        }

        if (
            Arr::exists($api, 'youtube.video') &&
            ($name = Arr::get($api['youtube.video'], 'snippet.title')) &&
            $settings->get('kilowhat-rich-embeds.youtubeApiPreviews')
        ) {
            $thumbnails = Arr::get($api['youtube.video'], 'snippet.thumbnails');

            $thumbnailUrl = Arr::get($thumbnails, 'medium.url') ?: Arr::get($thumbnails, 'default.url');

            $stats = [];

            $statistics = Arr::get($api['youtube.video'], 'statistics');

            if (Arr::exists($statistics, 'viewCount')) {
                $stats[] = [
                    'icon' => 'far fa-eye',
                    'label' => $translator->trans('kilowhat-rich-embeds.api.embed.youtube.video.stats.viewCount'),
                    'value' => Arr::get($statistics, 'viewCount'),
                    'number' => true,
                ];
            }

            if (Arr::exists($statistics, 'likeCount')) {
                $stats[] = [
                    'icon' => 'far fa-thumbs-up',
                    'label' => $translator->trans('kilowhat-rich-embeds.api.embed.youtube.video.stats.likeCount'),
                    'value' => Arr::get($statistics, 'likeCount'),
                    'number' => true,
                ];
            }

            if (Arr::exists($statistics, 'commentCount')) {
                $stats[] = [
                    'icon' => 'far fa-comments',
                    'label' => $translator->trans('kilowhat-rich-embeds.api.embed.youtube.video.stats.commentCount'),
                    'value' => Arr::get($statistics, 'commentCount'),
                    'number' => true,
                ];
            }

            if ($publishedAt = Arr::get($api['youtube.video'], 'snippet.publishedAt')) {
                $stats[] = [
                    'icon' => 'far fa-calendar',
                    'label' => $translator->trans('kilowhat-rich-embeds.api.embed.youtube.video.stats.publishedAt'),
                    'value' => $publishedAt,
                    'date' => true,
                ];
            }

            return [
                'siteName' => $translator->trans('kilowhat-rich-embeds.api.embed.youtube.siteName'),
                'contentType' => $translator->trans('kilowhat-rich-embeds.api.embed.youtube.video.type'),
                'title' => $name,
                'description' => Arr::get($api['youtube.video'], 'snippet.description'),
                'duration' => Arr::get($api['youtube.video'], 'contentDetails.duration'),
                'images' => $thumbnailUrl ? [
                    [
                        'src' => $thumbnailUrl,
                        'alt' => $translator->trans('kilowhat-rich-embeds.api.embed.youtube.video.thumbnailAlt'),
                    ],
                ] : [],
                'stats' => $stats,
            ];
        }

        if (
            Arr::exists($api, 'googledrive.file') &&
            ($name = Arr::get($api['googledrive.file'], 'name')) &&
            $settings->get('kilowhat-rich-embeds.googledriveApiPreviews')
        ) {
            $stats = [];

            if ($size = Arr::get($api['googledrive.file'], 'size')) {
                $stats[] = [
                    'icon' => 'far fa-file',
                    'label' => $translator->trans('kilowhat-rich-embeds.api.embed.googledrive.file.stats.size'),
                    'value' => $size,
                    'bytes' => true,
                ];
            }

            if ($createdTime = Arr::get($api['googledrive.file'], 'createdTime')) {
                $stats[] = [
                    'icon' => 'far fa-calendar',
                    'label' => $translator->trans('kilowhat-rich-embeds.api.embed.googledrive.file.stats.createdTime'),
                    'value' => $createdTime,
                    'date' => true,
                ];
            }

            if ($modifiedTime = Arr::get($api['googledrive.file'], 'modifiedTime')) {
                $shouldShowModifiedTime = true;

                if ($createdTime) {
                    try {
                        // hide this value if creation and edit dates are too close together
                        if (Carbon::parse($createdTime)->diffInMinutes($modifiedTime) < 10) {
                            $shouldShowModifiedTime = false;
                        }
                    } catch (\Exception $exception) {
                        // silence errors
                    }
                }

                if ($shouldShowModifiedTime) {
                    $stats[] = [
                        'icon' => 'fas fa-pen',
                        'label' => $translator->trans('kilowhat-rich-embeds.api.embed.googledrive.file.stats.modifiedTime'),
                        'value' => $modifiedTime,
                        'date' => true,
                    ];
                }
            }

            $thumbnailLink = Arr::get($api['googledrive.file'], 'thumbnailLink');

            // TODO: find a way to only do this for files that can't be browsed publicly
            if ($thumbnailLink) {
                if ($this->actor->hasPermission('kilowhat-rich-embeds.viewGoogleDriveThumbnails')) {
                    $urlGenerator = resolve(UrlGenerator::class);

                    // Replace actual link which expires with a link to our cached route
                    $thumbnailLink = $urlGenerator->to('api')->route('kilowhat-rich-embeds.google-drive-thumbnails', [
                        'id' => Arr::get($api['googledrive.file'], 'id'),
                    ]);
                } else {
                    $thumbnailLink = null;
                }
            }

            return [
                'icon' => Arr::get($api['googledrive.file'], 'iconLink'),
                'siteName' => $translator->trans('kilowhat-rich-embeds.api.embed.googledrive.siteName'),
                'contentType' => $translator->trans('kilowhat-rich-embeds.api.embed.googledrive.file.type'),
                'title' => $name,
                'images' => $thumbnailLink ? [
                    [
                        'src' => $thumbnailLink,
                        'alt' => $translator->trans('kilowhat-rich-embeds.api.embed.googledrive.file.thumbnailAlt'),
                    ],
                ] : [],
                'stats' => $stats,
            ];
        }

        if ($opengraphTitle = Arr::get($opengraph, 'title')) {
            return [
                'siteName' => $fallbackSiteName,
                'title' => $opengraphTitle,
                'description' => Arr::get($opengraph, 'description'),
                'images' => $this->uniqueAndLimitImages($opengraphImages, $settings),
            ];
        }

        $fallback = $embed->fallback ?: [];

        if (
            ($fallbackTitle = Arr::get($fallback, 'title')) &&
            $settings->get('kilowhat-rich-embeds.withoutOpengraph')
        ) {
            return [
                'siteName' => $fallbackSiteName,
                'title' => $fallbackTitle,
                'description' => Arr::get($fallback, 'description'),
                'images' => $this->uniqueAndLimitImages(array_map(function ($image) use ($fallbackSiteName) {
                    return [
                        'src' => Arr::get($image, 'src'),
                        'alt' => Arr::get($image, 'alt') ?: $fallbackSiteName,
                    ];
                }, Arr::get($fallback, 'images') ?: []), $settings),
            ];
        }

        return null;
    }

    protected function uniqueAndLimitImages(array $images, SettingsRepositoryInterface $settings): array
    {
        // Duplicate image filter is mostly meant for fallback parsers
        // But we've also seen misconfigured sites with duplicated opengraph tags
        $includedSrcs = [];
        $returnImages = [];

        $maxImages = (int)$settings->get('kilowhat-rich-embeds.maxImageCount');

        foreach ($images as $image) {
            if ($maxImages > 0 && count($returnImages) === $maxImages) {
                break;
            }

            // If an invalid URL was returned during parsing, the payload might contain NULL values
            if (is_null($image['src']) || in_array($image['src'], $includedSrcs)) {
                continue;
            }

            $includedSrcs[] = $image['src'];
            $returnImages[] = $image;
        }

        return $returnImages;
    }

    protected function finishFlarumPreview(array $preview, $api): array
    {
        if (Arr::exists($api, 'flarum.forum') && $title = Arr::get($api['flarum.forum'], 'data.attributes.title')) {
            $preview['siteName'] = $title;
        }

        return $preview;
    }
}
