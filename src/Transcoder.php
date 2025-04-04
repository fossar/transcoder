<?php

declare(strict_types=1);

namespace Ddeboer\Transcoder;

use Ddeboer\Transcoder\Exception\ExtensionMissingException;
use Ddeboer\Transcoder\Exception\UnsupportedEncodingException;

class Transcoder implements TranscoderInterface
{
    /**
     * @var array<string, TranscoderInterface>
     */
    private static array $chain;

    /**
     * @var TranscoderInterface[]
     */
    private array $transcoders = [];

    /**
     * @param TranscoderInterface[] $transcoders
     */
    public function __construct(array $transcoders)
    {
        $this->transcoders = $transcoders;
    }

    public function transcode(string $string, ?string $from = null, ?string $to = null): string
    {
        foreach ($this->transcoders as $transcoder) {
            try {
                return $transcoder->transcode($string, $from, $to);
            } catch (UnsupportedEncodingException $e) {
                // Ignore as long as the fallback transcoder is all right
            }
        }

        if (!isset($e)) {
            throw new UnsupportedEncodingException('of any kind', 'No transcoder provided.');
        }

        throw $e;
    }

    /**
     * Create a transcoder.
     *
     * @throws ExtensionMissingException
     */
    public static function create(string $defaultEncoding = 'UTF-8'): TranscoderInterface
    {
        if (isset(self::$chain[$defaultEncoding])) {
            return self::$chain[$defaultEncoding];
        }

        $transcoders = [];

        try {
            $transcoders[] = new MbTranscoder($defaultEncoding);
        } catch (ExtensionMissingException $mb) {
            // Ignore missing mbstring extension; fall back to iconv
        }

        try {
            $transcoders[] = new IconvTranscoder($defaultEncoding);
        } catch (ExtensionMissingException $iconv) {
            // Ignore missing iconv extension; hopefully, we have mbstring.
        }

        // Neither mbstring nor iconv
        if (count($transcoders) === 0) {
            throw new ExtensionMissingException('mbstring or iconv');
        }

        self::$chain[$defaultEncoding] = new self($transcoders);

        return self::$chain[$defaultEncoding];
    }
}
