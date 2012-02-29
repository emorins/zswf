<?php
/**
 * ZSwf.php
 *
 * @author   Shotaro Emori
 * @package  ZSwf
 */

define('ZSWF_PATH', dirname(__FILE__));

require_once ZSWF_PATH . '/ZSwf/Exception.php';
require_once ZSWF_PATH . '/ZSwf/Tag.php';
require_once ZSWF_PATH . '/ZSwf/Tags.php';
require_once ZSWF_PATH . '/ZSwf/ControlTag.php';
require_once ZSWF_PATH . '/ZSwf/ControlTag/SetBackgroundColor.php';
require_once ZSWF_PATH . '/ZSwf/Bitmap.php';
require_once ZSWF_PATH . '/ZSwf/Bitmap/DefineBitsJPEG2.php';
require_once ZSWF_PATH . '/ZSwf/Util/Bit.php';
require_once ZSWF_PATH . '/ZSwf/Util/Byte.php';
require_once ZSWF_PATH . '/ZSwf/Util/JPEG.php';

/**
 * ZSwf Class
 *
 * @author   Shotaro Emori
 * @package  ZSwf
 * @access   public
 */
class ZSwf
{
    /**#@+
     * @access private
     */

    /** @var string バイナリ */
    private $bin = '';

    /** @var boolean 圧縮・非圧縮 */
    private $compression = false;

    /** @var integer swfバージョン */
    private $version = 0;

    /** @var integer サイズ */
    private $size = 0;

    /** @var array RECT */
    private $rect = array();

    /** @var integer フレームレート */
    private $frameRate = 0;

    /** @var integer フレーム数 */
    private $frameLength = 0;

    /** @var array タグのリスト */
    private $tags;

    /**#@-*/

    /**
     * コンストラクタ
     *
     * @access public
     * @param  string $src swfファイルのパス
     * @throws ZSwf_Exception
     */
    public function __construct($src = null)
    {
        if (is_null($src)) {
            return;
        }
        $this->bin = file_get_contents($src);
        try{
            $this->parse($this->bin);
        } catch (ZSwf_Exception $e) {
            throw new ZSwf_Exception('パースに失敗しました:'. $e->getMessage());
        }
    }
    
    /**
     * Magic 圧縮・非圧縮の取得
     *
     * @access public
     * @return boolean
     */
    public function getCompression()
    {
        if ($this->compression === '465753') {
            $compression = false;
        } else if ($this->compression === '435753') {
            $compression = true;
        }
        return $compression;
    }

    /**
     * Magic 圧縮・非圧縮の設定
     *
     * @access public
     * @param  boolean $compression 圧縮・非圧縮
     * @return boolean
     */
    public function setCompression($compression)
    {
        if ($compression === false) {
            $this->compression = '465753';
        } else if ($compression === true) {
            $this->compression = '435753';
        }
    }

    /**
     * varsionの取得
     *
     * @access public
     * @return integer
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * varsionの設定
     *
     * @access public
     * @param  integer $version swfバージョン
     * @return integer
     */
    public function setVersion($version)
    {
        return $this->version = $version;
    }

    /**
     * sizeの取得
     *
     * @access public
     * @return integer
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * widthの取得
     *
     * @access public
     * @return integer
     */
    public function getWidth()
    {
        return $this->rect[1] - $this->rect[0];
    }

    /**
     * widthの設定
     *
     * @access public
     * @param  integer $width 横幅
     * @return integer
     */
    public function setWidth($width)
    {
        return $this->rect[1] = $width + $this->rect[0];
    }

    /**
     * heightの取得
     *
     * @access public
     * @return integer
     */
    public function getHeight()
    {
        return $this->rect[3] - $this->rect[2];
    }

    /**
     * heightの設定
     *
     * @access public
     * @param  integer $height 縦幅
     * @return integer
     */
    public function setHeight($height)
    {
        return $this->rect[3] = $height + $this->rect[2];
    }

    /**
     * フレームレートの取得
     *
     * @access public
     * @return integer
     */
    public function getFrameRate()
    {
        return $this->frameRate;
    }

    /**
     * フレームレートの設定
     *
     * @access public
     * @param  integer $frameRate フレームレート
     * @return integer 
     */
    public function setFrameRate($frameRate)
    {
        return $this->frameRate = $frameRate;
    }

    /**
     * rootのフレーム数の取得
     *
     * @access public
     * @return integer
     */
    public function getFrameLength()
    {
        return $this->frameLength;
    }

    /**
     * rootのフレーム数の設定
     *
     * @access public
     * @param  integer $frameLength フレーム数
     * @return integer
     */
    public function setFrameLength($frameLength)
    {
        return $this->frameLength = $frameLength;
    }

    /**
     * SetBackgroundColorの取得
     *
     * @access public
     * @return integer 16進RGB
     */
    public function getBackgroundColor()
    {
        $setBackgroundColor = $this->tags->get('ControlTag_SetBackgroundColor');
        return $setBackgroundColor[0]->getField('BackgroundColor');
    }

    /**
     * SetBackgroundColorの設定
     *
     * @access public
     * @param integer $color 16進RGB
     */
    public function setBackgroundColor($color)
    {
        $this->tags->replace(array('BackgroundColor' => $color), 'ControlTag_SetBackgroundColor');
    }

    /**
     * JPEG画像の取得
     *
     * @access public
     * @param  string $characterId  キャラクタID
     * @return string JPEGデータ
     */
    public function getJPEG($characterId)
    {
        $defineBitsJPEG2 = $this->tags->get('Bitmap_DefineBitsJPEG2', array('CharacterID' => $characterId));
        if (empty($defineBitsJPEG2)) {
            return null;
        }
        return $defineBitsJPEG2[0]->getField('ImageData');
    }

    /**
     * JPEG画像の入れ替え
     *
     * @access public
     * @param  string $bitmapData  入れ替える JPEGデータ
     * @param  string $characterId 入れ替え対象のキャラクタID
     */
    public function replaceJPEG($bitmapData, $characterId)
    {
        $this->tags->replace(array('ImageData' => $bitmapData), 'Bitmap_DefineBitsJPEG2', array('CharacterID' => $characterId));
    }

    /**
     * ビルド
     *
     * @access public
     * @return string swfバイナリ
     */
    public function build()
    {
        //header
        $compression = pack('H*', $this->compression);
        $version = chr($this->version);

        //movie header
        $length = 0;
        foreach ($this->rect as $point) {
            $_length = ZSwf_Util_Bit::getBitLength($point * 20);
            if($length < $_length) {
                $length = $_length;
            }
        }
        ZSwf_Util_Bit::resetOffset();
        ZSwf_Util_Bit::setBits($length, 5);
        ZSwf_Util_Bit::setBits($this->rect[0] * 20, $length);
        ZSwf_Util_Bit::setBits($this->rect[1] * 20, $length);
        ZSwf_Util_Bit::setBits($this->rect[2] * 20, $length);
        $rect = ZSwf_Util_Bit::setBits($this->rect[3] * 20, $length, true);

        $frameRate = chr(fmod($this->frameRate, 1) * 100) . chr(floor($this->frameRate));
        $frameLength = pack('v', $this->frameLength);

        //tag block
        $tagBlock = $this->tags->pack();

        //ファイルサイズの更新
        $movieHeaderAndTagBlock = $rect.$frameRate.$frameLength.$tagBlock;
        $this->size = strlen($movieHeaderAndTagBlock) + 8;
        $size = pack('V', $this->size);

        //CWSなら圧縮
        if ($this->getCompression()) {
            $movieHeaderAndTagBlock = gzcompress($movieHeaderAndTagBlock);
        }

        //swfバイナリの完成
        $swf = $compression.$version.$size.$movieHeaderAndTagBlock;
        return $swf;
    }

    /**
     * バイナリの解析
     *
     * @access public
     * @param  string $src swfのバイナリデータ
     * @throws ZSwf_Exception
     */
    public function parse($bin)
    {
        if ($this->bin === '') {
            $this->bin = $bin;
        }

        ZSwf_Util_Byte::resetOffset();

        //compression
        $arr = unpack('H*', ZSwf_Util_Byte::getBytes($bin, 3));
        $this->compression = $arr[1];
        if ($this->compression !== '465753' && $this->compression !== '435753') {
            throw new ZSwf_Exception('SWFのファイルフォーマットがおかしいです');
        }
 
        //version
        $this->version = ord(ZSwf_Util_Byte::getBytes($bin, 1));

        //size
        $arr = unpack('V*', ZSwf_Util_Byte::getBytes($bin, 4));
        $this->size = $arr[1];

        //圧縮swfなら解凍
        if ($this->getCompression() === true) {
            $bin = ZSwf_Util_Byte::getBytes($bin, 8, 0) . gzuncompress(ZSwf_Util_Byte::getBytes($bin, strlen($bin), 8));
        }
        
        //stage size
        $chanku = ZSwf_Util_Bit::getBits($bin, 5, 8, 0);
        $this->rect = array(ZSwf_Util_Bit::getBits($bin, $chanku) / 20,
                            ZSwf_Util_Bit::getBits($bin, $chanku) / 20,
                            ZSwf_Util_Bit::getBits($bin, $chanku) / 20,
                            ZSwf_Util_Bit::getBits($bin, $chanku) / 20,
                            );

        ZSwf_Util_Byte::setByteOffset(ZSwf_Util_Bit::getByteOffset() + 1);
    
        //frame rate
        $float = ZSwf_Util_Byte::getBytes($bin, 1);
        $integer = ZSwf_Util_Byte::getBytes($bin, 1);
        $this->frameRate = ord($integer) + ord($float) / 100;

        //frame length
        $arr = unpack('v', ZSwf_Util_Byte::getBytes($bin, 2));
        $this->frameLength = $arr[1];

        //tag block
        $this->tags = new ZSwf_Tags(ZSwf_Util_Byte::getBytes($bin, strlen($bin)));
    }

    /**
     * 独自の圧縮方法を試みる
     *
     * このメソッドはいわるゆFWS|CWSなFlashが通常するZLIBな圧縮ではなく、
     * Flashコンパイラが吐くswfをさらに最適化し、圧縮を試みるメソッドである。
     * このメソッドは
     *   1.SWFファイルフォーマットに準拠する
     *   2.最適化前と最適化後と再生環境が変化しない
     *   3.もちろん挙動にも変化がない
     * ことを条件とする。
     *
     * @access public
     * @return string swfバイナリ
     */
    public function compact()
    {
        //Contents部のサイズを計算しTag Formatを求め、TLCを再構築する。
        //1KB〜2KBの削減
        $this->tags->compact();

        //...

        //ビルド
        return $this->build();
    }
}
