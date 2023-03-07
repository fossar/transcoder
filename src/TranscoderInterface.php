<?php

declare(strict_types=1);

namespace Ddeboer\Transcoder;

use Ddeboer\Transcoder\Exception\UnsupportedEncodingException;

interface TranscoderInterface
{
    /**
     * Transcode a string from one encoding into another.
     *
     * @param string $from Source encoding (optional)
     * @param string $to Target encoding (optional)
     *
     * @throws UnsupportedEncodingException
     */
    public function transcode(string $string, ?string $from = null, ?string $to = null): string;
}
