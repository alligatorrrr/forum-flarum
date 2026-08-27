<?php

namespace Kilowhat\RichEmbeds;

use Flarum\Api\Serializer\ForumSerializer;
use Flarum\Settings\SettingsRepositoryInterface;

class ForumAttributes
{
    protected $settings;

    public function __construct(SettingsRepositoryInterface $settings)
    {
        $this->settings = $settings;
    }

    public function __invoke(ForumSerializer $serializer): array
    {
        $canUse = $serializer->getActor()->hasPermission('kilowhat-rich-embeds.useOnOwnPost');

        return [
            'richEmbedsLayout' => $this->settings->get('kilowhat-rich-embeds.layout') ?: 'block',
            'richEmbedsStyle' => $this->settings->get('kilowhat-rich-embeds.style') ?: 'vertical',
            'richEmbedsImageProxy' => (bool)$this->settings->get('kilowhat-rich-embeds.imageProxy'),
            'richEmbedsWithoutOpengraph' => (bool)$this->settings->get('kilowhat-rich-embeds.withoutOpengraph'),
            'richEmbedsYoutubePlayer' => (bool)$this->settings->get('kilowhat-rich-embeds.youtubePlayer'),
            'richEmbedsYoutubeNoCookie' => (bool)$this->settings->get('kilowhat-rich-embeds.youtubeNoCookie'),
            'canUseRichEmbeds' => $canUse,
            'canDisableRichEmbeds' => $serializer->getActor()->hasPermission('kilowhat-rich-embeds.disableOnOwnPost'),
            'richEmbedsImage' => $canUse && $this->settings->get('kilowhat-rich-embeds.imageEmbeds'),
        ];
    }
}
