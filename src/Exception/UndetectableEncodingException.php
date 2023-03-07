<?php

declare(strict_types=1);

namespace Ddeboer\Transcoder\Exception;

class UndetectableEncodingException extends \RuntimeException
{
    public function __construct(string $string, string $error)
    {
        parent::__construct(sprintf('Encoding for %s is undetectable: %s', $string, $error));
    }
}
