<?php
/**
 * SetBackgroundColor.php
 *
 * @author     Shotaro Emori
 * @package    ZSwf
 * @subpackage ControlTag
 */

/**
 * SetBackgroundColor Class
 *
 * @author     Shotaro Emori
 * @package    ZSwf
 * @subpackage ControlTag
 * @access     public
 */
class ZSwf_ControlTag_SetBackgroundColor extends ZSwf_ControlTag
{
    /** @const タグ名 */
    const NAME = 'SetBackgroundColor';

    /** @const タグ番号 */
    const NUMBER = 9;

    /** @const 対応swfバージョン */
    const VERSION = 1;

    /**
     * タグのContents部の各フィールドのリスト
     * @access protected
     * @var    array
     */
    protected $fieldList = array();

    /**
     * コンストラクタ
     *
     * @access public
     * @param  array $tlc TLC構造体
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
        $tlc['contents'] = pack('H*', $this->fieldList['BackgroundColor']);
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
     * @param array $tlc TLC構造体
     */
    protected function unpack($tlc)
    {
        $arr = unpack('H*', $tlc['contents']);
        $this->fieldList['BackgroundColor'] = $arr[1];
    }
}