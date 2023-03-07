<?php

declare(strict_types=1);

namespace Ddeboer\Transcoder\Tests;

use Ddeboer\Transcoder\Transcoder;
use Ddeboer\Transcoder\TranscoderInterface;

class TranscoderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var TranscoderInterface
     */
    private $transcoder;

    /**
     * @before
     */
    protected function doSetUp(): void
    {
        $this->transcoder = Transcoder::create();
    }

    /**
     * @dataProvider getStrings
     */
    public function testTranscode(string $string, string $encoding): void
    {
        $result = $this->transcoder->transcode($string, 'UTF-8', $encoding);
        $this->assertEquals($string, $this->transcoder->transcode($result, $encoding));
    }

    /** @return iterable<array{string, string}> */
    public function getStrings(): iterable
    {
        yield ['España', 'UTF-8'];
        yield ['bla', 'windows-1257']; // Encoding only supported by iconv
    }
}
