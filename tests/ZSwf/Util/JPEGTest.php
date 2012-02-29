<?php
/**
 * Test class for JPEG.
 * 
 */
class JPEGTest extends PHPUnit_Framework_TestCase
{
    /**
     * Provider
     *
     */
    public function provider()
    {
        return array(
                     array(ZSWF_TEST_PATH . '/fixtures/replace.jpg', array(
                                                                           array('tag' => 'ffd8', 'lenght' => null, 'contents' => null),
                                                                           array('tag' => 'ffe0', 'lenght' => 16,   'contents' => '292b33609054f87cba2dfa32c6d2c89a'),
                                                                           array('tag' => 'ffe1', 'lenght' => 6147, 'contents' => '27b92ee41e4e33720daf9b29c664ce70'),
                                                                           array('tag' => 'ffe1', 'lenght' => 7467, 'contents' => '8201ca6f336d66d61553507cbf4fb8a0'),
                                                                           array('tag' => 'ffed', 'lenght' => 8678, 'contents' => '306dfcbeda7068ac85aadbea6734f1db'),
                                                                           array('tag' => 'ffdb', 'lenght' => 67,   'contents' => 'a3a96add050fc51a2b3ce59a9a491034'),
                                                                           array('tag' => 'ffdb', 'lenght' => 67,   'contents' => '67f62471b0b39c4d785eeb278e9cc391'),
                                                                           array('tag' => 'ffc0', 'lenght' => 17,   'contents' => 'f833aac009e3eac0a96717aacccbe61a'),
                                                                           array('tag' => 'ffc4', 'lenght' => 30,   'contents' => '6a7f8962f7ef4736acb70687f66b40b7'),
                                                                           array('tag' => 'ffc4', 'lenght' => 89,   'contents' => 'ebca1a2433b14579f276aef9a4e2a15f'),
                                                                           array('tag' => 'ffc4', 'lenght' => 21,   'contents' => 'c556ca913ae2425170fde7fad934ebda'),
                                                                           array('tag' => 'ffc4', 'lenght' => 22,   'contents' => 'e89ca304415346572ac6ae79ef08e63c'),
                                                                           array('tag' => 'ffda', 'lenght' => null, 'contents' => '46390e15606b918dd886e9876b4bcd4f'),
                                                                           array('tag' => 'ffd9', 'lenght' => null, 'contents' => null),
                                                                           )),
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
     * parseのテスト
     *
     * @dataProvider provider
     */
    public function testParse($path, Array $value)
    {
        $tlc = ZSwf_Util_JPEG::parse(file_get_contents($path));
        for ($i = 0; $i < count($tlc); $i++) {
            $this->assertEquals($tlc[$i]['tag'], $value[$i]['tag'], 'マーカー名が正常に取得できていない');
            $this->assertEquals($tlc[$i]['lenght'], $value[$i]['lenght'], 'セグメント長が正常に取得できていない');
            if ($tlc[$i]['contents'] !== null) {
                $this->assertEquals(md5($tlc[$i]['contents']), $value[$i]['contents'], 'データ部が正常に取得できていない');
            }
        }
    }

    /**
     * buildのテスト
     *
     * @dataProvider provider
     */
    public function testBuild($path, Array $value)
    {
        $tlc = ZSwf_Util_JPEG::parse(file_get_contents($path));
        $bin = ZSwf_Util_JPEG::build($tlc);
        $this->assertEquals($bin, file_get_contents($path));
    }
}
