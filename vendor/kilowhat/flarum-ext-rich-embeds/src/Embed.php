<?php

namespace Kilowhat\RichEmbeds;

use Carbon\Carbon;
use Flarum\Database\AbstractModel;

/**
 * @property int $id
 * @property string $url
 * @property string $url_hash
 * @property string $final_url
 * @property int $http_status
 * @property string $error
 * @property string $mime
 * @property array $opengraph
 * @property array $icons
 * @property array $fallback
 * @property array $exif
 * @property array $api_resource
 * @property int $width
 * @property int $height
 * @property int $size
 * @property Carbon $created_at
 * @property Carbon $retrieved_at
 */
class Embed extends AbstractModel
{
    protected $table = 'kilowhat_rich_embeds';

    protected $casts = [
        'opengraph' => 'array',
        'icons' => 'array',
        'fallback' => 'array',
        'exif' => 'array',
        'api_resource' => 'array',
        'width' => 'int',
        'height' => 'int',
        'size' => 'int',
        'created_at' => 'datetime',
        'retrieved_at' => 'datetime',
    ];

    // We don't rely on Schema\Builder::$defaultStringLength because we need larger values
    // We will also use those values for validation/truncating
    public static $urlStringLength = 2048; // Chosen as the most common max between browsers. Needs to be large enough to maximize compatibility
    public static $mimeStringLength = 64; // Chosen as a short value, we don't really care if it gets truncated

    /**
     * Formats a URL in a way that the whitelist and database can handle
     * This method expects an already valid URL
     * @param string $url
     * @return string
     */
    public static function normalizeUrl(string $url): string
    {
        return $url; // TODO
    }

    /**
     * Shorthand to compute the SHA1 hash of a given URL. Should use the normalized URL
     * @param string $url
     * @return string
     */
    public static function hashUrl(string $url): string
    {
        return sha1($url, true);
    }
}
