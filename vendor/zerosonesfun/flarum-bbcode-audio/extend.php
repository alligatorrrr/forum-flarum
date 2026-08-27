<?php

/*
 * bbcode audio is a Flarum extension created by Billy Wilcosky.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * For instructions, please view the README file.
 */

namespace ZerosOnesFun\Audio;

use Flarum\Extend;
use s9e\TextFormatter\Configurator;

return [
    (new Extend\Formatter)
        ->configure(function (Configurator $config) {
             $config->BBCodes->addCustom(
               '[audio mp3="{URL1?}" m4a="{URL2?}" wav="{URL3?}" ogg="{URL4?}" flac="{URL5?}" webm="{URL6?}" width="{NUMBER?;defaultValue=100}" download="{SIMPLETEXT?;defaultValue=nodownload}"]',
               '<p><audio class="bbaudio inline-exclude" style="width:{NUMBER}%;" controls controlsList="{SIMPLETEXT}">
                        <source src="{URL1}" type="audio/mpeg">
                        <source src="{URL2}" type="audio/mp4">
                        <source src="{URL3}" type="audio/wav">
                        <source src="{URL4}" type="audio/ogg">
                        <source src="{URL5}" type="audio/flac">
                        <source src="{URL6}" type="audio/webm">
                </audio></p>'
            );
        }),
    (new Extend\ServiceProvider())
        ->register(Upload\AudioServiceProvider::class)
];
