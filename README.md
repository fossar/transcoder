# Transcoder

[![Packagist Version](https://img.shields.io/packagist/v/fossar/transcoder)](https://packagist.org/packages/fossar/transcoder)

## Introduction

This is a wrapper around PHP’s `mb_convert_encoding` and `iconv` functions. This library adds:

- fallback from `mb` to `iconv` for encodings it does not support
- conversion of warnings to proper exceptions.

## Installation

The recommended way to install the Transcoder library is through [Composer](http://getcomposer.org):

```bash
$ composer require fossar/transcoder
```

This command requires you to have Composer installed globally, as explained in the [installation chapter](https://getcomposer.org/doc/00-intro.md) of the Composer documentation.

## Usage

### Basics

Create the right transcoder for your platform and translate a string to ISO-8859-1 encoding:

```php
use Ddeboer\Transcoder\Transcoder;

$transcoder = Transcoder::create();
$result = $transcoder->transcode('España', 'iso-8859-1');
```

You can also manually instantiate a transcoder of your liking:

```php
use Ddeboer\Transcoder\MbTranscoder;

$transcoder = new MbTranscoder();
```

Or:

```php
use Ddeboer\Transcoder\IconvTranscoder;

$transcoder = new IconvTranscoder();
```

### Source encoding

The second argument accepts source encoding and can actually be omitted or passed `null`.

```php
$transcoder->transcode('España');
```

In that case, however, the behaviour is backend-specific:

- `IconvTranscoder` will use the encoding of the current [locale](https://www.php.net/manual/en/function.setlocale.php) of the process.
- `MbTranscoder` will try to detect encoding from a list based on the value of [`mbstring.language`](https://www.php.net/manual/en/mbstring.configuration.php#ini.mbstring.language) setting. By default, this tries ASCII, followed by UTF-8. The number of [supported languages](https://github.com/php/php-src/blob/d61d21ad57d04b91a1155153811d16ea982fa106/ext/mbstring/mbstring.c#L88-L147) is limited though and the encoding tables often overlap so the detection might be unreliable.

As you can see, this is mostly useless for western languages. You will get much more reliable results when you specify the source encoding explicitly.

### Target encoding

Specify a default target encoding as the first argument to `create()`:

```php
use Ddeboer\Transcoder\Transcoder;

$isoTranscoder = Transcoder::create('iso-8859-1');
```

Alternatively, specify a target encoding as the third argument in a `transcode()` call:

```php
use Ddeboer\Transcoder\Transcoder;

$transcoder->transcode('España', 'iso-8859-1', 'UTF-8');
```

### Error handling

PHP’s `mv_convert_encoding` and `iconv` are inconvenient to use because they generate notices and warnings instead of proper exceptions. This library fixes that:

```php
use Ddeboer\Transcoder\Exception\UndetectableEncodingException;
use Ddeboer\Transcoder\Exception\UnsupportedEncodingException;
use Ddeboer\Transcoder\Exception\IllegalCharacterException;

$input = 'España';

try {
    $transcoder->transcode($input, 'utf-8', 'not-a-real-encoding');
} catch (UnsupportedEncodingException $e) {
    // ‘not-a-real-encoding’ is an unsupported encoding
}

try {
    $transcoder->transcode('Illegal quotes: ‘ ’', 'utf-8', 'iso-8859-1');
} catch (IllegalCharacterException $e) {
    // Curly quotes ‘ ’ are illegal in ISO-8859-1
}

try {
    $transcoder->transcode($input);
} catch (UndetectableEncodingException $e) {
    // Failed to automatically detect $input’s encoding (mb) or not a valid string in current locale locale (iconv)
}
```

### Transcoder fallback

In general, `mb_convert_encoding` is faster than `iconv`. However, as `iconv` supports more encodings than `mb_convert_encoding`, it makes sense to combine the two.

So, the Transcoder returned from `create()`:

- uses `mb_convert_encoding` if the [mbstring](http://php.net/manual/en/book.mbstring.php) PHP extension is installed;
- if not, it uses `iconv` instead if the [iconv](http://php.net/manual/en/book.iconv.php) extension is installed;
- if both the mbstring and iconv extension are available, the Transcoder will first try `mb_convert_encoding` and fall back to `iconv`.
