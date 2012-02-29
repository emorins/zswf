<?php
/**
 * Bit.php
 *
 * @author   Shotaro Emori
 * @package  ZSwf
 * @subpackage Util
 */

/**
 * ZSwf_Util_Bit Class
 *
 * @author     Shotaro Emori
 * @package    ZSwf
 * @subpackage Util
 * @access     public
 */
class ZSwf_Util_Bit
{
    /**
     * バイト列のオフセット値
     * @static
     * @access private
     */
    static private $byteOffset = 0;

    /**
     * ビット列のオフセット値
     * @static
     * @access private
     */
    static private $bitOffset = 0;

    /**
     * 現在のバイト列のオフセットを返す
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
     * 現在のビット列のオフセットを返す
     *
     * @static
     * @access public
     * @return integer オフセット
     */
    static public function getBitOffset()
    {
        return self::$bitOffset;
    }

    /**
     * オフセットのリセット
     *
     * @static
     * @access public
     */
    static public function resetOffset()
    {
        self::$bitOffset = 0;
        self::$byteOffset = 0;
    }

    /**
     * 指定した引数をビット長を合わせてバイナリにパック
     *
     * @static
     * @access    public
     * @staticvar string  $byte   バイト列
     * @param     integer $int    数値
     * @param     integer $length ビット長
     * @param     boolean $end    終端かどうか。trueなら残りを0で埋めてバイナリを返す
     * @return    string
     */
    static public function setBits($int, $length, $end = false)
    {
        static $byte;
        for ($i = $length - 1 ; $i >= 0 ; $i--) {
            //バイト長が足りないなら追加
            if (strlen($byte) < self::$byteOffset + 1) {
                $byte .= str_pad(chr(0), 1);
            }
            $n = ($int >> $i) & 1;
            if ($n > 0) {
                $value = ord($byte{self::$byteOffset});
                $value |= 1 << (7 - self::$bitOffset);
                $byte{self::$byteOffset} = chr($value);
            }
            self::$bitOffset += 1;
            if (8 <= self::$bitOffset) {
                self::$byteOffset += 1;
                self::$bitOffset  = 0;
            }
        }
        if ($end === true) {
            $_byte = $byte;
            $byte = null;
            self::$byteOffset = 0;
            self::$bitOffset  = 0;
            return $_byte;
        }
    }

    /**
     * length分のビット列を返す
     *
     * @static
     * @access public
     * @param  string  $bin         バイナリ
     * @param  integer $length      ビット長
     * @param  integer $_byteOffset バイトオフセット
     * @param  integer $_bitOffset  ビットオフセット
     * @return string  ビット列
     */
    static public function getBits($bin, $length = 0, $_byteOffset = null, $_bitOffset = null)
    {
        if (!is_null($_byteOffset)) {
            self::$byteOffset = $_byteOffset;
        }
        if (!is_null($_bitOffset)) {
            self::$bitOffset = $_bitOffset;
        }
        $byte = ord($bin{self::$byteOffset});
        $value = 0;
        for ($i = 0; $i < $length; $i++) {
            $value <<= 1;
            $value |= 1 & ($byte >> (7 - self::$bitOffset));
            self::$bitOffset++;
            if (8 <= self::$bitOffset) {
                self::$byteOffset++;
                self::$bitOffset = 0;
                $byte = ord($bin{self::$byteOffset});
            }
        }
        return $value;
    }

    /**
     * 引数を符号付きで表現するのに必要なビット長を返す
     *
     * @static
     * @access public
     * @param  integer $int 数値
     * @return integer ビット長
     */
    static public function getBitLength($int)
    {
        if ($int == -1) {
            $length = 1;
            return;
        }
        if ($int < -1) {
            $int = -1 - $int;
        }
        if ($int >= 0) {
            for ($i = 0 ; $int ; $i++) {
                $int >>= 1;
            }
            $length = 1 + $i;
        }
        return $length;
    }
}