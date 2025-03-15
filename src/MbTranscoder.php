<?php

declare(strict_types=1);

namespace Ddeboer\Transcoder;

use Ddeboer\Transcoder\Exception\ExtensionMissingException;
use Ddeboer\Transcoder\Exception\UndetectableEncodingException;
use Ddeboer\Transcoder\Exception\UnsupportedEncodingException;

class MbTranscoder implements TranscoderInterface
{
    /**
     * @var array<string, int>
     */
    private static array $encodings;

    private string $defaultEncoding;

    /**
     * Create a Mb-based transcoder.
     *
     * @throws ExtensionMissingException
     */
    public function __construct(string $defaultEncoding = 'UTF-8')
    {
        if (!function_exists('mb_convert_encoding')) {
            throw new ExtensionMissingException('mb');
        }

        if (!isset(self::$encodings)) {
            self::$encodings = array_change_key_case(
                array_flip(mb_list_encodings()),
                CASE_LOWER
            );
        }

        $this->assertSupported($defaultEncoding, false);
        $this->defaultEncoding = $defaultEncoding;
    }

    /**
     * @param array<string>|string|null $from
     */
    public function transcode(string $string, $from = null, ?string $to = null): string
    {
        if ($from) {
            if (is_array($from)) {
                array_map([$this, 'assertSupported'], $from);
            } else {
                $this->assertSupported($from);
            }
        } else {
            $from = 'auto';
        }

        if ($to) {
            $this->assertSupported($to, false);
        }

        if ($from === 'auto') {
            $from = mb_detect_encoding($string, 'auto', true);
        }

        if ($from === false) {
            throw new UndetectableEncodingException($string, 'Unable to detect character encoding');
        }

        $result = mb_convert_encoding(
            $string,
            $to ?: $this->defaultEncoding,
            $from
        );

        return $result;
    }

    /**
     * @throws UnsupportedEncodingException
     */
    private function assertSupported(string $encoding, bool $allowAuto = true): void
    {
        if (!$this->isSupported($encoding, $allowAuto)) {
            throw new UnsupportedEncodingException($encoding);
        }
    }

    private function isSupported(string $encoding, bool $allowAuto): bool
    {
        return ($allowAuto && $encoding === 'auto') || isset(self::$encodings[strtolower($encoding)]);
    }
}
