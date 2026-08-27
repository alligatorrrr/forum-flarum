<?php

namespace Kilowhat\RichEmbeds;

use Flarum\Settings\SettingsRepositoryInterface;
use s9e\TextFormatter\Configurator;

class ConfigureFormatter
{
    protected $settings;

    public function __construct(SettingsRepositoryInterface $settings)
    {
        $this->settings = $settings;
    }

    public function __invoke(Configurator $config)
    {
        $config->BBcodes->addCustom(
            '[RICH-URL={URL;useContent}]{TEXT}[/RICH-URL]',
            '<a class="rich-url" href="{@rich-url}"><xsl:apply-templates /></a>'
        );

        // Add a class to recognise links that come from the URL tag
        // Add the hash attribute so TextFormatter doesn't try to edit a link in which we have mounted a Mithril component
        $config->tags->get('URL')->template = '<a class="inline-url" href="{@url}" data-s9e-livepreview-hash=""><xsl:apply-templates/></a>';

        if ($config->tags->exists('IMG') && $this->settings->get('kilowhat-rich-embeds.imageEmbeds')) {
            $config->tags->get('IMG')->template = <<<XML
<xsl:element name="span">
    <xsl:attribute name="class">inline-img</xsl:attribute>
    <xsl:attribute name="data-s9e-livepreview-hash"/>
    <xsl:attribute name="data-src"><xsl:value-of select="@src"/></xsl:attribute>
    <xsl:attribute name="data-alt"><xsl:value-of select="@alt"/></xsl:attribute>
    <xsl:if test="@height">
        <xsl:attribute name="data-height"><xsl:value-of select="@height"/></xsl:attribute>
    </xsl:if>
    <xsl:if test="@width">
        <xsl:attribute name="data-width"><xsl:value-of select="@width"/></xsl:attribute>
    </xsl:if>
    <img src="{@src}" title="{@title}" alt="{@alt}">
        <xsl:copy-of select="@height"/>
        <xsl:copy-of select="@width"/>
    </img>
</xsl:element>
XML;
        }
    }
}
