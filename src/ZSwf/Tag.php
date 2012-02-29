<?php
/**
 * Tag.php
 *
 * @author   Shotaro Emori
 * @package  ZSwf
 */

/**
 * ZSwf_Tag Class
 *
 * @author   Shotaro Emori
 * @package  ZSwf
 * @access   public
 * @abstract
 */
abstract class ZSwf_Tag
{
    /**
     * タグのContents部の各フィールドのリスト
     * @access protected
     * @var    array
     */
    protected $fieldList = array();

    /**
     * コンストラクタ
     *
     * @access   public
     * @abstract
     * @param array $tlc TLC構造体
     */
    abstract public function __construct($tlc);

    /**
     * フィールドを設定
     *
     * @access   public
     * @abstract
     * @param array $fieldList array('Field Key' => 'Field Value')
     */
    abstract public function setField(Array $fieldList);

    /**
     * フィールドの取得
     *
     * @access   public
     * @abstract
     * @param  string フィールド名
     * @return string フィールドの値
     */
    abstract public function getField($fieldName);

    /**
     * fieldListメンバを元にContents部分をバイナリにパック
     * そこからlength,formatを計算し、TLC構造体を返す
     *
     * @access   public
     * @abstract
     * @return array
     */
    abstract public function pack();

    /**
     * Contents部分のバイナリを解析し、
     * 各フィールド名をキーにした連想配列をfieldListメンバに代入
     *
     * @access   public
     * @abstract
     * @param array $tlc TLC構造体
     */
    abstract protected function unpack($tlc);
}