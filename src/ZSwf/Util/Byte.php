<?php
/**
 * Byte.php
 *
 * @author   Shotaro Emori
 * @package  ZSwf
 * @subpackage Util
 */

/**
 * ZSwf_Util_Byte Class
 *
 * @author     Shotaro Emori
 * @package    ZSwf
 * @subpackage Util
 * @access     public
 */
class ZSwf_Util_Byte
{
    /**
     * バイト列のオフセット値
     * @static
     * @access private
     */
    static private $byteOffset = 0;

    /**
     * 現在のバイト列のオフセット値を返す
     *
     * @static
     * @access public
     * @return integer オフセット
     */
    static public function getByteOffset()
    {
        return self::$byteOffset;
    }

    /**
     * バイト列のオフセットのセット
     *
     * @static
     * @access public
     * @param  integer $offset
     */
    static public function setByteOffset($offset)
    {
        self::$byteOffset = $offset;
    }

    /**
     * オフセットのリセット
     *
     * @static
     * @access public
     */
    static public function resetOffset()
    {
        self::$byteOffset = 0;
    }

    /**
     * length分のバイト列を返す
     *
     * @static
     * @access public
     * @param  string  $bin         バイナリ
     * @param  integer $length      ビット長
     * @param  integer $_byteOffset バイトオフセット
     * @param  integer $_bitOffset  ビットオフセット
     * @return string  ビット列
     */
    static public function getBytes($bin, $length, $_byteOffset = null)
    {
        if (!is_null($_byteOffset)) {
            self::$byteOffset = $_byteOffset;
        }
        $bytes = substr($bin, self::$byteOffset, $length);
        self::$byteOffset += $length;
        return $bytes;
    }
}