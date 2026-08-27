<?php

namespace Kilowhat\RichEmbeds;

use DOMDocument;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\UriResolver;
use GuzzleHttp\Utils;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Psr\Http\Message\UriInterface;

/**
 * Parses https://ogp.me/ to a JSON object
 */
class Parser
{
    /**
     * @var DOMDocument
     */
    protected $document;

    /**
     * @var UriInterface
     */
    protected $baseUri;

    public function load(string $html, UriInterface $baseUri)
    {
        $old_libxml_error = libxml_use_internal_errors(true);

        $this->document = new DOMDocument('1.0', 'UTF-8');
        $this->document->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        //$this->document->encoding = 'utf-8';

        libxml_use_internal_errors($old_libxml_error);

        // https://developer.mozilla.org/en-US/docs/Web/HTML/Element/base
        $bases = $this->document->getElementsByTagName('base');

        if ($bases->count()) {
            $baseUri = UriResolver::resolve($baseUri, new Uri($bases->item(0)->getAttribute('href')));
        }

        $this->baseUri = $baseUri;
    }

    public function opengraph(): array
    {
        $nodes = $this->document->getElementsByTagName('meta');

        $opengraph = [];
        $image = null;

        foreach ($nodes as $node) {
            $content = $node->getAttribute('content');

            // None of the attributes make sense if the value is empty so we might as well skip them
            if (empty($content)) {
                continue;
            }

            switch ($node->getAttribute('property')) {
                case 'og:type':
                    $opengraph['type'] = $this->whitelist($content, [
                        'music.song',
                        'music.album',
                        'music.playlist',
                        'music.radio_station',
                        'video.movie',
                        'video.episode',
                        'video.tv_show',
                        'video.other',
                        'article',
                        'book',
                        'profile',
                        'website',
                    ]);
                    break;
                case 'og:title':
                    $opengraph['title'] = trim($content);
                    break;
                case 'og:url':
                    $opengraph['url'] = $this->url($content);
                    break;
                case 'og:image':
                case 'og:image:url':
                    if (!is_null($image)) {
                        $opengraph['images'][] = $image;
                    }

                    $image = [
                        'url' => $this->url($content),
                    ];
                    break;
                case 'og:image:secure_url':
                    if (is_null($image)) {
                        break;
                    }

                    $image['secure_url'] = $this->url($content);
                    break;
                case 'og:image:type':
                    if (is_null($image)) {
                        break;
                    }

                    // TODO: validation for MIME type?
                    $image['type'] = $content;
                    break;
                case 'og:image:width':
                    if (is_null($image)) {
                        break;
                    }

                    $image['width'] = (int)$content;
                    break;
                case 'og:image:height':
                    if (is_null($image)) {
                        break;
                    }

                    $image['height'] = (int)$content;
                    break;
                case 'og:image:alt':
                    if (is_null($image)) {
                        break;
                    }

                    $image['alt'] = trim($content);
                    break;
                case 'og:description':
                    $opengraph['description'] = trim($content);
                    break;
                case 'og:site_name':
                    $opengraph['site_name'] = trim($content);
                    break;
            }
        }

        if (!is_null($image)) {
            $opengraph['images'][] = $image;
        }

        return $opengraph;
    }

    protected function whitelist(string $value, array $whitelist): ?string
    {
        if (in_array($value, $whitelist, true)) {
            return $value;
        }

        return null;
    }

    protected function url(string $value): ?string
    {
        if (Str::startsWith($value, '//')) {
            $value = 'https:' . $value;
        }

        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        return null;
    }

    public function icons(): array
    {
        $nodes = $this->document->getElementsByTagName('link');

        $icons = [];

        foreach ($nodes as $node) {
            if (preg_match('~( |^)icon( |$)~', $node->getAttribute('rel')) !== 1) {
                continue;
            }

            $icon = [
                'href' => (string)UriResolver::resolve($this->baseUri, new Uri($node->getAttribute('href'))),
            ];

            // https://developer.mozilla.org/en-US/docs/Web/HTML/Element/link#attr-sizes
            $sizes = trim($node->getAttribute('sizes'));

            if ($sizes === 'any') {
                $icon['sizes'] = 'any';
            } else {
                $sizesList = explode(' ', $sizes);

                foreach ($sizesList as $size) {
                    if (preg_match('~\s*([0-9]+)[xX]([0-9]+)\s*~', $size, $matches) === 1) {
                        $icon['sizes'][] = [
                            'width' => (int)$matches[1],
                            'height' => (int)$matches[2],
                        ];
                    }
                }
            }

            $type = trim($node->getAttribute('type'));

            if ($type) {
                $icon['type'] = $type;
            }

            $icons[] = $icon;
        }

        return $icons;
    }

    public function fallback(): array
    {
        $fallback = [];

        $titleNodes = $this->document->getElementsByTagName('title');

        if ($titleNodes->count()) {
            $title = trim($titleNodes->item(0)->textContent);

            if ($title) {
                $fallback['title'] = $title;
            }
        }

        $metaNodes = $this->document->getElementsByTagName('meta');

        foreach ($metaNodes as $node) {
            if ($node->getAttribute('name') === 'description') {
                $description = trim($node->getAttribute('content'));

                if ($description) {
                    $fallback['description'] = $description;
                }
            }
        }

        $imgNodes = $this->document->getElementsByTagName('img');

        foreach ($imgNodes as $node) {
            $image = [
                'src' => (string)UriResolver::resolve($this->baseUri, new Uri($node->getAttribute('src')))
            ];

            $alt = trim($node->getAttribute('alt'));

            if ($alt) {
                $image['alt'] = $alt;
            }

            $fallback['images'][] = $image;
        }

        return $fallback;
    }

    public function flarumApiPayload(): ?array
    {
        $payloadTag = $this->document->getElementById('flarum-json-payload');

        if (!$payloadTag) {
            return null;
        }

        $content = $payloadTag->textContent;

        try {
            $data = Utils::jsonDecode($content, true);
        } catch (\Exception $exception) {
            // If we can't parse the JSON, just skip it
            return null;
        }

        $returnPayload = [];

        $apiDocument = Arr::get($data, 'apiDocument');

        if (Arr::get($apiDocument, 'data.type') === 'discussions') {
            $returnPayload['flarum.discussion'] = $apiDocument;
        }

        if (Arr::get($apiDocument, 'data.type') === 'users') {
            $returnPayload['flarum.user'] = $apiDocument;
        }

        $forumPayload = Arr::first(Arr::get($data, 'resources'), function ($resource) {
            return Arr::get($resource, 'type') === 'forums';
        });

        if ($forumPayload) {
            $returnPayload['flarum.forum'] = [
                // Wrap single record into a data key so it's the same format as for discussion/user
                'data' => $forumPayload,
            ];
        }

        if (count($returnPayload) === 0) {
            return null;
        }

        return $returnPayload;
    }
}
