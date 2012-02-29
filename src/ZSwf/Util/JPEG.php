<?php
/**
 * JPEG.php
 *
 * @author     Shotaro Emori
 * @package    ZSwf
 * @subpackage Util
 */

/**
 * ZSwf_Util_JPEG Class
 *
 * @author     Shotaro Emori
 * @package    ZSwf
 * @subpackage Util
 * @access     public
 */
class ZSwf_Util_JPEG
{
    /**
     * JPEGを生成
     *
     * @static
     * @access public
     * @param  array   マーカー
     * @return string  jpegバイナリ
     */
    static public function build(Array $tlcList)
    {
        $bin = '';
        ZSwf_Util_Bit::resetOffset();
        foreach ($tlcList as $tlc) {
            $bin .= pack('H*', $tlc['tag']);
            if ($tlc['lenght'] !== null) {
                $bin .= ZSwf_Util_Bit::setBits($tlc['lenght'], 16, true);
            }
            if ($tlc['contents'] !== null) {
                $bin .= $tlc['contents'];
            }
            
        }
        return $bin;
    }

    /**
     * JPEGのパース
     *
     * @static
     * @access public
     * @param  string  jpegバイナリ
     * @return array   マーカー
     */
    static public function parse($bin)
    {
        $tlc = array();
        ZSwf_Util_Byte::resetOffset();
        while (true) {
            $arr = unpack('H*', ZSwf_Util_Byte::getBytes($bin, 2));
            $maker = $arr[1];
            if (!$maker) {
                break;
            }
            if ($maker === 'ffd8') {
                $tlc[] = array('tag' => $maker, 'lenght' => null, 'contents' => null);
                continue;
            } elseif ($maker === 'ffd9') {
                $tlc[] = array('tag' => $maker, 'lenght' => null, 'contents' => null);
                break;
            } elseif ($maker === 'ffda') {
                $offset = ZSwf_Util_Byte::getByteOffset();
                $end = strpos(ZSwf_Util_Byte::getBytes($bin, strlen($bin)), pack('H*', 'ffd9'));
                ZSwf_Util_Byte::setByteOffset($offset);
                $tlc[] = array('tag' => $maker, 'lenght' => null, 'contents' => ZSwf_Util_Byte::getBytes($bin, $end));
            } else {
                $arr = unpack('H*', ZSwf_Util_Byte::getBytes($bin, 2));
                $lenght = hexdec($arr[1]);
                $tlc[] = array('tag' => $maker, 'lenght' => $lenght, 'contents' => ZSwf_Util_Byte::getBytes($bin, $lenght - 2));
            }
        }
        return $tlc;
    }
}
