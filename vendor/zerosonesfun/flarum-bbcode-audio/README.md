# Audio BBCode

A [Flarum](http://flarum.org) extension. Adds an [audio] BBCode. Can be used with Friends of Flarum Upload extension. If used with Upload extension, set up audio rules and choose the Audio template.

### Usage
~~~
[audio mp3=song.mp3 ogg=song.ogg]
[audio mp3=song.mp3]
[audio ogg=song.ogg]
[audio m4a=song.m4a]
[audio wav=song.wav]
[audio flac=song.flac]
[audio webm=song.webm]
[audio mp3=song.mp3 width=50]
[audio mp3=song.mp3 download=download]
~~~
(You may put a mp3 and ogg file together for maximum browser compatibility, or just post a mp3, ogg, wav, flac, webm or m4a file alone. Optionally set the width and/or allow downloads. The download attribute only works in certain browsers, most notably Chrome.)

### Install

```sh
composer require zerosonesfun/flarum-bbcode-audio:"*"
```

### Update

```sh
composer update zerosonesfun/flarum-bbcode-audio:"*"
php flarum cache:clear
```

### Uninstall
```sh
composer remove zerosonesfun/flarum-bbcode-audio
php flarum cache:clear
```

### Links

- [Packagist](https://packagist.org/packages/zerosonesfun/flarum-bbcode-audio)
- [GitHub](https://github.com/zerosonesfun/flarum-bbcode-audio)
- [Donate](https://www.wilcosky.com)