<?php

namespace Ddeboer\Transcoder\Exception;

class UndetectableEncodingException extends \RuntimeException
{
    /**
     * @param string $string
     * @param string $error
     */
    public function __construct($string, $error)
    {
        parent::__construct(sprintf('Encoding for %s is undetectable: %s', $string, $error));
    }
}
