<?php

namespace Ddeboer\Transcoder\Exception;

class UnsupportedEncodingException extends \RuntimeException
{
    /**
     * @param string  $encoding
     * @param ?string $message
     */
    public function __construct($encoding, $message = null)
    {
        $error = sprintf('Encoding %s is unsupported on this platform', $encoding);
        if ($message) {
            $error .= ': ' . $message;
        }

        parent::__construct($error);
    }
}
