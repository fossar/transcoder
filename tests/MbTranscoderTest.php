<?php

namespace Ddeboer\Transcoder\Tests;

use Ddeboer\Transcoder\MbTranscoder;

class MbTranscoderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var MbTranscoder
     */
    private $transcoder;

    /**
     * @before
     */
    protected function doSetUp()
    {
        $this->transcoder = new MbTranscoder();
    }

    public function testTranscodeUnsupportedFromEncoding()
    {
        $this->expectException(\Ddeboer\Transcoder\Exception\UnsupportedEncodingException::class);
        $this->expectExceptionMessage('bad-encoding');
        $this->transcoder->transcode('bla', 'bad-encoding');
    }

    public function testTranscodeUnsupportedToEncoding()
    {
        $this->expectException(\Ddeboer\Transcoder\Exception\UnsupportedEncodingException::class);
        $this->expectExceptionMessage('bad-encoding');
        $this->transcoder->transcode('bla', 'utf-8', 'bad-encoding');
    }

    public function testDetectEncoding()
    {
        $oldLanguage = ini_get('mbstring.language');
        ini_set('mbstring.language', 'ru');

        $utf8 = 'пирожки';
        $this->assertEquals($utf8, $this->transcoder->transcode($utf8));

        $koi8r = $this->transcoder->transcode($utf8, 'utf-8', 'koi8-r');
        $this->assertEquals($utf8, $this->transcoder->transcode($koi8r));

        ini_set('mbstring.language', $oldLanguage);
    }

    public function testUndetectableEncoding()
    {
        $this->expectException(\Ddeboer\Transcoder\Exception\UndetectableEncodingException::class);
        $this->expectExceptionMessage('is undetectable');
        $result = $this->transcoder->transcode(
            '‘curly quotes make this incompatible with 1252’',
            null,
            'windows-1252'
        );
        $this->transcoder->transcode($result);
    }

    /**
     * @dataProvider getStrings
     *
     * @param string $string
     * @param string $encoding
     */
    public function testTranscode($string, $encoding)
    {
        $result = $this->transcoder->transcode($string, null, $encoding);
        $this->assertEquals($string, $this->transcoder->transcode($result, $encoding));
    }

    public function getStrings()
    {
        return [
            ['‘España’', 'windows-1252'],
            ['España', 'iso-8859-1'],
        ];
    }
}
