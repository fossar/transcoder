<?php

declare(strict_types=1);

namespace Ddeboer\Transcoder\Exception;

class UnsupportedEncodingException extends \RuntimeException
{
    public function __construct(string $encoding, ?string $message = null)
    {
        $error = sprintf('Encoding %s is unsupported on this platform', $encoding);
        if ($message !== null) {
            $error .= ': ' . $message;
        }

        parent::__construct($error);
    }
}
