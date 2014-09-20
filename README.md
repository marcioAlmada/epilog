<h1 align="center">
  <img alt="Epilog" align="center" src="https://dl.dropboxusercontent.com/u/49549530/epilog/epilog-logo.png">
</h1>

[![Build Status][t-badge]][t-link]
[![Coverage Status][c-badge]][c-link]
[![Scrutinizer Quality Score][s-badge]][s-link]
[![Latest Stable Version][v-badge]][p-link]
[![Total Downloads][d-badge]][p-link]
[![License][l-badge]][p-link]
[![SensioLabsInsight][sl-badge]][sl-link]

The lightweight, themeable and interactive PSR-3 log viewer. Monitor monologs with style:

[![asciinema](https://dl.dropboxusercontent.com/u/49549530/epilog/asciinema.png)](https://asciinema.org/a/12309?autoplay=true&speed=1.5)

## Install

:bomb: Not ready to rumble yet. Try at your own risk!

As a composer project dependency:

```json
{
    "require": {
        "marc/epilog": "dev-master"
    }
}
```

As a composer global package:

```bash
composer global require marc/epilog:dev-master
```

Or build the phar yourself:

```bash
# install box2 -> https://github.com/box-project/box2
git clone https://github.com/marcioAlmada/epilog.git
cd epilog
composer install
make # phar will available at dist/ folder
sudo make install # will put epilog at /usr/local/bin/ folder
```

### Quick test

Run **epilog** with `--pretend` option and tail a fake log stream. Check if output looks good:

```bash
epilog . --pretend
```

## Usage

Basic usage is:

```bash
epilog /path/to/monolog/file.log
```

While epilog is monitoring the log file (or a fake stream), hit `[return]` to see a nice interactive menu:

```
$ [ ⏎ ]

Woot! Epilog here. Please type a theme number, a valid regexp or a valid flag:

[#] load another theme:

    1:chaplin       2:forest
    3:scrapbook     4:punchcard
    5:sunset        6:sunrise
    7:traffic       8:usa

[ r ] load random theme from list above.
[ i ] toggle invert theme.
[ d ] toggle debug mode.
[ c ] clear screen.
[ - ] reset regexp filter.
[ q ] quit.
```

Epilog is still pre alpha. A more detailed manual will be added soon.

## Themes

Epilog themes are very simple `yml` files with hooks where you can put ANSI color escape sequences and literal text.
Those hooks will decorate a log line template and [nyancatyze](http://youtu.be/QH2-TGUlwu4) log lines.
Here is a theme example:

```yaml
name: Punched Card
author: Márcio Almada
theme:
    extends: default
    # the log line template
    # template tags are: {date}, {level}, {logger}, {message}, {context}, {extra}
    template: "{level} {date} {message} [{logger}] [{context}] [{extra}]"
    # literal string that will be prepended to entire line
    prepend: ""
    # literal string that will be appended to entire line
    append: "\e[0m\n"
    # level section
    level:
        padding: 10
        DEBUG:
            prepend: "        • \e[2m"
        INFO:
            prepend: "       •  \e[2m"
        NOTICE:
            prepend: "      •   \e[2m"
        WARNING:
            prepend: "     •    \e[2m"
        ERROR:
            prepend: "    •     \e[1m"
        CRITICAL:
            prepend: "   •      \e[1m"
        ALERT:
            prepend: "  •       \e[1m"
        EMERGENCY:
            prepend: " •        \e[1m"
        DEFAULT:
            prepend: " -------- \e[1m"
```

Which will make log lines look like the following, when interpreted:

![punchcard theme](https://dl.dropboxusercontent.com/u/49549530/epilog/punchcard.png)

## Roadmap

- [x] Basic functionalities
- [ ] Add `--app` to allow easy framework integration. Ex: `epilog --app laravel`
- [ ] Add `--listen` option to aggretate log entries through a REST API. Ex: `epilog --listen 3000`
- [ ] Add `--server` to view logs in a browser instead of terminal `epilog <file> --server`
- [ ] Add more themes, better theme support, etc
- [ ] Bother with windows ... anyone?
- [ ] Other cool things, probably
- [ ] Release stable version

## Contributions

Found a bug? Have an improvement? Take a look at the [issues](https://github.com/marcioAlmada/epilog/issues).

### Guide
 
0. Fork [marc/epilog](https://github.com/marcioAlmada/epilog/fork)
0. Clone forked repository
0. Install composer dependencies `$ composer install`
0. Run unit tests `$ phpunit`
0. Modify code: correct bug, implement features
0. Back to step 4

## Copyright

Copyright (c) 2014 Márcio Almada. Distributed under the terms of an MIT-style license.
See LICENSE for details.

[t-link]: https://travis-ci.org/marcioAlmada/epilog "Travis Build"
[c-link]: https://coveralls.io/r/marcioAlmada/epilog?branch=master "Code Coverage"
[s-link]: https://scrutinizer-ci.com/g/marcioAlmada/epilog/?branch=master "Code Quality"
[p-link]: https://packagist.org/packages/marc/epilog "Packagist"
[sl-link]: https://insight.sensiolabs.com/projects/02d1fd01-8a70-4fe4-a550-381a3c0e33f3 "Sensiolabs Insight"

[t-badge]: https://travis-ci.org/marcioAlmada/epilog.png?branch=master
[c-badge]: https://coveralls.io/repos/marcioAlmada/epilog/badge.png?branch=master
[s-badge]: https://scrutinizer-ci.com/g/marcioAlmada/epilog/badges/quality-score.png?b=master
[v-badge]: https://poser.pugx.org/marc/epilog/v/stable.png
[d-badge]: https://poser.pugx.org/marc/epilog/downloads.png
[l-badge]: https://poser.pugx.org/marc/epilog/license.png
[sl-badge]: https://insight.sensiolabs.com/projects/02d1fd01-8a70-4fe4-a550-381a3c0e33f3/mini.png
