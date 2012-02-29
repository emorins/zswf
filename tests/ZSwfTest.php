<?php
/**
 * Test class for ZSwf.
 * 
 * TODO:set*メソッドはとりあえず限界値テストだけしておく
 */
class ZSwfTest extends PHPUnit_Framework_TestCase
{
    /**
     * Provider
     *
     */
    public function provider()
    {
        return array(
            array(new ZSwf(ZSWF_TEST_PATH . '/fixtures/sample.swf'), array(false, 4, 77428, 240, 270, 11, 56, '02f7f7', 'sample.jpg')),
            array(new ZSwf(ZSWF_TEST_PATH . '/fixtures/sample_flashlite11_as1.swf'), array(false, 4, 58718, 550, 400, 24, 1, 'ffffff', 'sample_flashlite.jpg')),
            array(new ZSwf(ZSWF_TEST_PATH . '/fixtures/sample_flashlite20_as1.swf'), array(false, 7, 58714, 550, 400, 24, 1, 'ffffff', 'sample_flashlite.jpg')),
            array(new ZSwf(ZSWF_TEST_PATH . '/fixtures/sample_flashlite20_as1_compression.swf'), array(true, 7, 58714, 550, 400, 24, 1, 'ffffff', 'sample_flashlite.jpg')),
            array(new ZSwf(ZSWF_TEST_PATH . '/fixtures/sample_flashlite20_as2.swf'), array(false, 7, 58714, 550, 400, 24, 1, 'ffffff', 'sample_flashlite.jpg')),
            array(new ZSwf(ZSWF_TEST_PATH . '/fixtures/sample_flashlite20_as2_compression.swf'), array(true, 7, 58714, 550, 400, 24, 1, 'ffffff', 'sample_flashlite.jpg')),
        );
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     */
    protected function setUp()
    {
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * getCompressionのテスト
     *
     * @dataProvider provider
     */
    public function testGetCompression(ZSwf $zswf, Array $value)
    {
        $this->assertEquals($zswf->getCompression(), $value[0], '圧縮・非圧縮が正常に解析できていません');
    }

    /**
     * setCompressionのテスト
     *
     * @dataProvider provider
     */
    public function testSetCompression(ZSwf $zswf, Array $value)
    {
        $zswf->setCompression(true);
        $zswf->parse($zswf->build());
        $this->assertTrue($zswf->getCompression(), '圧縮swfになっていません');

        $zswf->setCompression(false);
        $zswf->parse($zswf->build());
        $this->assertFalse($zswf->getCompression(), '非圧縮swfになっていません');
    }

    /**
     * getVersionのテスト
     *
     * @dataProvider provider
     */
    public function testGetVersion(ZSwf $zswf, Array $value)
    {
        $this->assertEquals($zswf->getVersion(), $value[1], 'swfのバージョンが正常に解析できていません');
    }

    /**
     * setVersionのテスト
     *
     * @dataProvider provider
     */
    public function testSetVersion(ZSwf $zswf, Array $value)
    {
        $zswf->setVersion(1);
        $zswf->parse($zswf->build());
        $this->assertEquals($zswf->getVersion(), 1, 'swfバージョン1とかにしてみたけどなってない');

        $zswf->setVersion(10);
        $zswf->parse($zswf->build());
        $this->assertEquals($zswf->getVersion(), 10, 'swfバージョン10とかにしてみたけどなってない');
    }

    /**
     * getSizeのテスト
     *
     * @dataProvider provider
     */
    public function testGetSize(ZSwf $zswf, Array $value)
    {
        $this->assertEquals($zswf->getSize(), $value[2], 'swfバイナリのサイズが正常に解析できていません');
    }

    /**
     * getWidthのテスト
     *
     * @dataProvider provider
     */
    public function testGetWidth(ZSwf $zswf, Array $value)
    {
        $this->assertEquals($zswf->getWidth(), $value[3], '横幅が正常に解析できていません');
    }

    /**
     * setWidthのテスト
     *
     * @dataProvider provider
     */
    public function testSetWidth(ZSwf $zswf, Array $value)
    {
        $zswf->setWidth(1);
        $zswf->parse($zswf->build());
        $this->assertEquals($zswf->getWidth(), 1, '横幅を最小サイズである1にしてみたけどなってない');

        $zswf->setWidth(2880);
        $zswf->parse($zswf->build());
        $this->assertEquals($zswf->getWidth(), 2880, '横幅を最大サイズである2880にしてみたけどなってない');
    }

    /**
     * getHeightのテスト
     *
     * @dataProvider provider
     */
    public function testGetHeight(ZSwf $zswf, Array $value)
    {
        $this->assertEquals($zswf->getHeight(), $value[4], '縦幅が正常に解析できていません');
    }

    /**
     * setHeightのテスト
     *
     * @dataProvider provider
     */
    public function testSetHeight(ZSwf $zswf, Array $value)
    {
        $zswf->setHeight(1);
        $zswf->parse($zswf->build());
        $this->assertEquals($zswf->getHeight(), 1, '縦幅を最小サイズである1にしてみたけどなってない');

        $zswf->setHeight(2880);
        $zswf->parse($zswf->build());
        $this->assertEquals($zswf->getHeight(), 2880, '縦幅を最大サイズである2880にしてみたけどなってない');
    }

    /**
     * getFrameRateのテスト
     *
     * @dataProvider provider
     */
    public function testGetFrameRate(ZSwf $zswf, Array $value)
    {
        $this->assertEquals($zswf->getFrameRate(), $value[5], 'フレームレートが正常に解析できていません');
    }

    /**
     * setFrameRateのテスト
     *
     * @dataProvider provider
     */
    public function testSetFrameRate(ZSwf $zswf, Array $value)
    {
        $zswf->setFrameRate(0.01);
        $zswf->parse($zswf->build());
        $this->assertEquals($zswf->getFrameRate(), 0.01, 'フレームレートを最小値である0.01にしてみたがなってない');

        $zswf->setFrameRate(120);
        $zswf->parse($zswf->build());
        $this->assertEquals($zswf->getFrameRate(), 120, 'フレームレートを最大値である120とかにしてみたがなってない');
    }

    /**
     * getFrameLengthのテスト
     *
     * @dataProvider provider
     */
    public function testGetFrameLength(ZSwf $zswf, Array $value)
    {
        $this->assertEquals($zswf->getFrameLength(), $value[6], 'rootのフレーム数が正常に解析できていません');
    }

    /**
     * setFrameLengthのテスト
     *
     * @dataProvider provider
     */
    public function testSetFrameLength(ZSwf $zswf, Array $value)
    {
        $zswf->setFrameLength(1);
        $zswf->parse($zswf->build());
        $this->assertEquals($zswf->getFrameLength(), 1, 'rootのフレーム数を最小値である1にしてみたがなってない');

        $zswf->setFrameLength(16000);
        $zswf->parse($zswf->build());
        $this->assertEquals($zswf->getFrameLength(), 16000, 'rootのフレーム数を最大値である16000にしてみたがなってない');
    }

    /**
     * getBackgroundColorのテスト
     *
     * @dataProvider provider
     */
    public function testGetBackgroundColor(ZSwf $zswf, Array $value)
    {
        $this->assertEquals($zswf->getBackgroundColor(), $value[7], '背景色が正常に解析できていません');
    }

    /**
     * setBackgroundColorのテスト
     *
     * @dataProvider provider
     */
    public function testSetBackgroundColor(ZSwf $zswf, Array $value)
    {
        $zswf->setBackgroundColor('000000');
        $zswf->parse($zswf->build());
        $this->assertEquals($zswf->getBackgroundColor(), '000000', '背景色を黒にしてみたけどなってない');

        $zswf->setBackgroundColor('ffffff');
        $zswf->parse($zswf->build());
        $this->assertEquals($zswf->getBackgroundColor(), 'ffffff',  '背景色を白にしてたけどなってない');
    }

    /**
     * getJPEGのテスト
     *
     * @dataProvider provider
     */
    public function testGetJPEG(ZSwf $zswf, Array $value)
    {
        $this->assertEquals($zswf->getJPEG(1), file_get_contents(ZSWF_TEST_PATH . '/fixtures/' . $value[8]), 'JPEG画像が正常に取り出せていません');
        $this->assertNull($zswf->getJPEG(0), '存在しないはずのJPEG画像を取り出せている');
    }

    /**
     * replaceJPEGのテスト
     *
     * @dataProvider provider
     */
    public function testReplaceJPEG(ZSwf $zswf, Array $value)
    {
        $zswf->replaceJPEG(file_get_contents(ZSWF_TEST_PATH . '/fixtures/replace.jpg'), 1);
        $zswf->parse($zswf->build());        
        $this->assertEquals($zswf->getJPEG(1), file_get_contents(ZSWF_TEST_PATH . '/fixtures/replace.jpg'), 'JPEG画像が正常に入れ替わっていません');
    }

    /**
     * buildのテスト
     *
     */
    public function testBuild()
    {
        $zswf1 = new ZSwf();
        $zswf1->parse(file_get_contents(ZSWF_TEST_PATH . '/fixtures/sample.swf'));
        $zswf2 = new ZSwf(ZSWF_TEST_PATH . '/fixtures/sample.swf');
        $this->assertEquals($zswf1->build(), $zswf2->build());
    }

    /**
     * parseのテスト
     *
     */
    public function testParse()
    {
        $zswf1 = new ZSwf();
        $zswf1->parse(file_get_contents(ZSWF_TEST_PATH . '/fixtures/sample.swf'));
        $zswf2 = new ZSwf(ZSWF_TEST_PATH . '/fixtures/sample.swf');
        $this->assertTrue($zswf1 == $zswf2);
    }

    /**
     * compactのテスト
     *
     */
    public function testCompact()
    {
        $zswf = new ZSwf(ZSWF_TEST_PATH . '/fixtures/sample.swf');
        $bool = strlen($zswf->build()) > strlen($zswf->compact());
        $this->assertTrue($bool);
     }

    /**
     * ZSwf_Exceptionの例外テスト
     *
     * @expectedException ZSwf_Exception
     */
    public function testZSwf_Exception()
    {
        $zswf = new ZSwf(ZSWF_TEST_PATH . '/fixtures/sample_error.swf');
    }
}
