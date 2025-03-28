<?php

declare(strict_types=1);

namespace Ddeboer\Transcoder\Tests;

use Ddeboer\Transcoder\IconvTranscoder;

class IconvTranscoderTest extends \PHPUnit\Framework\TestCase
{
    private IconvTranscoder $transcoder;

    /**
     * @before
     */
    protected function doSetUp(): void
    {
        $this->transcoder = new IconvTranscoder();
        // Passing null (empty encoding name) to iconv makes it detect encoding from locale.
        // The phpunit-bridge sets locale to C for consistency but that implies ASCII.
        // This file uses UTF-8 so we have to set the locale accordingly.
        $this->setLocale(\LC_ALL, 'C.UTF-8');
    }

    public function testTranscodeUnsupportedFromEncoding(): void
    {
        $this->expectException(\Ddeboer\Transcoder\Exception\UnsupportedEncodingException::class);
        $this->expectExceptionMessage('bad-encoding');
        $this->transcoder->transcode('bla', 'bad-encoding');
    }

    public function testDetectEncoding(): void
    {
        $isoLatin1 = $this->transcoder->transcode('España', null, 'iso-8859-1');
        $this->assertEquals($isoLatin1, "Espa\xf1a");
    }

    public function testTranscodeIllegalCharacter(): void
    {
        $this->expectException(\Ddeboer\Transcoder\Exception\IllegalCharacterException::class);
        $this->transcoder->transcode('“', 'utf-8', 'iso-8859-1');
    }

    /**
     * @dataProvider getStrings
     */
    public function testTranscode(string $string, string $encoding): void
    {
        $result = $this->transcoder->transcode($string, 'utf-8', $encoding);
        $this->assertEquals($string, $this->transcoder->transcode($result, $encoding));
    }

    /** @return iterable<array{string, string}> */
    public function getStrings(): iterable
    {
        yield ['España', 'iso-8859-1'];
    }
}
