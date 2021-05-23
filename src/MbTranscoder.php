<?php

namespace Ddeboer\Transcoder;

use Ddeboer\Transcoder\Exception\ExtensionMissingException;
use Ddeboer\Transcoder\Exception\UndetectableEncodingException;
use Ddeboer\Transcoder\Exception\UnsupportedEncodingException;

class MbTranscoder implements TranscoderInterface
{
    /**
     * @var array<string, int>
     */
    private static $encodings;

    /**
     * @var string
     */
    private $defaultEncoding;

    /**
     * Create a Mb-based transcoder.
     *
     * @param string $defaultEncoding
     *
     * @throws ExtensionMissingException
     */
    public function __construct($defaultEncoding = 'UTF-8')
    {
        if (!function_exists('mb_convert_encoding')) {
            throw new ExtensionMissingException('mb');
        }

        if (null === self::$encodings) {
            self::$encodings = array_change_key_case(
                array_flip(mb_list_encodings()),
                CASE_LOWER
            );
        }

        $this->assertSupported($defaultEncoding, false);
        $this->defaultEncoding = $defaultEncoding;
    }

    /**
     * {@inheritdoc}
     */
    public function transcode($string, $from = null, $to = null)
    {
        if ($from) {
            if (is_array($from)) {
                array_map([$this, 'assertSupported'], $from);
            } else {
                $this->assertSupported($from);
            }
        }

        if ($to) {
            $this->assertSupported($to, false);
        }

        $handleErrors = !$from || 'auto' === $from;
        if ($handleErrors) {
            set_error_handler(
                function ($no, $warning) use ($string) {
                    throw new UndetectableEncodingException($string, $warning);
                },
                E_WARNING
            );
        }

        try {
            $result = mb_convert_encoding(
                $string,
                $to ?: $this->defaultEncoding,
                $from ?: 'auto'
            );
        } finally {
            if ($handleErrors) {
                restore_error_handler();
            }
        }

        return $result;
    }

    /**
     * @param string $encoding
     * @param bool $allowAuto
     *
     * @return void
     *
     * @throws UnsupportedEncodingException
     */
    private function assertSupported($encoding, $allowAuto = true)
    {
        if (!$this->isSupported($encoding, $allowAuto)) {
            throw new UnsupportedEncodingException($encoding);
        }
    }

    /**
     * @param string $encoding
     * @param bool $allowAuto
     *
     * @return bool
     */
    private function isSupported($encoding, $allowAuto)
    {
        return ($allowAuto && $encoding === 'auto') || isset(self::$encodings[strtolower($encoding)]);
    }
}
