<?php
/**
 * DefineBitsJPEG2.php
 *
 * @author     Shotaro Emori
 * @package    ZSwf
 * @subpackage Bitmap
 */

/**
 * DefineBitsJPEG2 Class
 * DefineBitsJPEG2について
 * - ImageDataフィールドには3つのフォーマットがある
 * -- 生JPEG  ※おそらくSWF v8以降。携帯では不可。
 * -- erroneous header(0xFF, 0xD9, 0xFF, 0xD8) + 生JPEG  ※SWF v8以前で対応
 * -- SOI +JPEGTables(量子化テーブル + ハフマンテーブル) + EOI + SOI + DefineBits(残りのJPEGデータ)+ EOI
 * - GIFとPNGも入れられる ※SWF v8以降
 *
 * @author     Shotaro Emori
 * @package    ZSwf
 * @subpackage Bitmap
 * @access     public
 */
class ZSwf_Bitmap_DefineBitsJPEG2 extends ZSwf_Bitmap
{
    /** @const タグ名 */
    const NAME = 'DefineBitsJPEG2';

    /** @const タグ番号 */
    const NUMBER = 21;

    /** @const 対応swfバージョン */
    const VERSION = 2;

    /**
     * タグのContents部の各フィールドのリスト
     * @access protected
     * @var    array
     */
    protected $fieldList = array();

    /**
     * ImageDataのフォーマット
     * 0:生JPEG
     * 1:erroneous header(0xFF, 0xD9, 0xFF, 0xD8) + 生JPEG
     * 2:SOI +JPEGTables(量子化テーブル + ハフマンテーブル) + EOI + SOI + DefineBits(残りのJPEGデータ)+ EOI
     *
     * @access private
     * @var    integer
     */
    private $imageDataFormat = 0;

    /**
     * コンストラクタ
     *
     * @access public
     * @param array $tlc TLC構造体
     */
    public function __construct($tlc)
    {
        $this->unpack($tlc);
    }

    /**
     * フィールドを設定
     *
     * @access public
     * @param  array $fieldList array('Field Key' => 'Field Value')
     */
    public function setField(Array $fieldList)
    {
        foreach ($fieldList as $key => $value) {
            $this->fieldList[$key] = $value;
        }
    }

    /**
     * フィールドの取得
     *
     * @access public
     * @param  string フィールド名
     * @return string フィールドの値
     */
    public function getField($fieldName)
    {
        if (!isset($this->fieldList[$fieldName])) {
            return null;
        }
        return $this->fieldList[$fieldName];
    }

    /**
     * fieldListメンバを元にContents部分をバイナリにパック
     * そこからlength,formatを計算し、TLC構造体を返す
     *
     * @access public
     * @return array
     */
    public function pack()
    {
        $tlc = array();
        $tlc['tag'] = self::NUMBER;
        $tlc['contents'] = pack('v', $this->fieldList['CharacterID']);
        if ($this->imageDataFormat === 1) {
            $this->fieldList['ImageData'] = pack('H*', 'ffd9ffd8') . $this->fieldList['ImageData'];
        } elseif ($this->imageDataFormat === 2) {
            //TODO:未実装
        }
        $tlc['contents'] .= $this->fieldList['ImageData'];
        $tlc['length'] = strlen($tlc['contents']);
        $tlc['format'] = 0;
        if ($tlc['length'] >= 0x3f) {
            $tlc['format'] = 1;
        }
        return $tlc;
    }

    /**
     * Contents部分のバイナリを解析し、
     * 各フィールド名をキーにした連想配列をfieldListメンバに代入
     *
     * @access protected
     * @param  array $tlc TLC構造体
     */
    protected function unpack($tlc)
    {
        ZSwf_Util_Byte::resetOffset();
        $arr = unpack('v', ZSwf_Util_Byte::getBytes($tlc['contents'], 2));
        $this->fieldList['CharacterID'] = $arr[1];
        /**
         * erroneous header形式
         */
        $arr = unpack('H*', ZSwf_Util_Byte::getBytes($tlc['contents'], 4));
        if ($arr[1] === 'ffd9ffd8') {
            $this->fieldList['ImageData'] = ZSwf_Util_Byte::getBytes($tlc['contents'], strlen($tlc['contents']) - 2, 6);
            $this->imageDataFormat = 1;
            return;
        }
        /**
         * JPEGTables + DefineBits形式
         * TODO:未実装
         */
        //$this->imageDataFormat = 2;

        /**
         * 生JPEG
         */
        $this->fieldList['ImageData'] = ZSwf_Util_Byte::getBytes($tlc['contents'], strlen($tlc['contents']) - 2, 2);
        $this->imageDataFormat = 0;
        return;
    }
}