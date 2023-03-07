<?php

declare(strict_types=1);

namespace Ddeboer\Transcoder;

use Ddeboer\Transcoder\Exception\ExtensionMissingException;
use Ddeboer\Transcoder\Exception\IllegalCharacterException;
use Ddeboer\Transcoder\Exception\UnsupportedEncodingException;

class IconvTranscoder implements TranscoderInterface
{
    /**
     * @var string
     */
    private $defaultEncoding;

    /**
     * Create an Iconv-based transcoder.
     *
     * @throws ExtensionMissingException
     */
    public function __construct(string $defaultEncoding = 'UTF-8')
    {
        if (!function_exists('iconv')) {
            throw new ExtensionMissingException('iconv');
        }

        $this->defaultEncoding = $defaultEncoding;
    }

    /**
     * {@inheritdoc}
     */
    public function transcode(string $string, ?string $from = null, ?string $to = null): string
    {
        set_error_handler(
            function (int $no, string $message) use ($string): void {
                if (1 === preg_match('/Wrong (charset|encoding), conversion (.+) is/', $message, $matches)) {
                    throw new UnsupportedEncodingException($matches[1], $message);
                } else {
                    throw new IllegalCharacterException($string, $message);
                }
            },
            E_NOTICE | E_USER_NOTICE | E_WARNING
        );

        try {
            $result = iconv($from ?? '', $to ?? $this->defaultEncoding, $string);
        } finally {
            restore_error_handler();
        }

        return $result;
    }
}
