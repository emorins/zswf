<?php
/**
 * Tags.php
 *
 * @author   Shotaro Emori
 * @package  ZSwf
 */

/**
 * ZSwf_Tags Class
 *
 * @author   Shotaro Emori
 * @package  ZSwf
 * @access   public
 */
class ZSwf_Tags
{
    /**
     * TLCが入った連想配列のリスト
     * @access private
     * @var    array
     */
    private $tagList = array();

    /**
     * コンストラクタ
     * Tag Block部分のバイナリを受け取り、展開
     *
     * @access public
     * @param  string $bin Tag Block部分のバイナリ
     */
    public function __construct($bin)
    {
        $this->unpack($bin);
    }

    /**
     * 引数にマッチしたSWF TagをTagオブジェクトにして格納した配列を返す
     *
     * @access public
     * @param  string $tagName  Tag名
     * @param  array  $filter   マッチさせる条件
     * @return array  マッチした全てのSWF TagのTag型配列
     */
    public function get($tagName, Array $filter = array())
    {
        $className = 'Zswf_' . $tagName;
        $tagObjectList = array();
        for ($i = 0; $i < count($this->tagList); $i++) {
            if ($this->tagList[$i]['tag'] === $className::NUMBER) {
                $tagObject = new $className($this->tagList[$i]);
                //filter
                foreach ($filter as $key => $value) {
                    if ($tagObject->getField($key) !== $value) {
                        continue 2;
                    }
                }
                $tagObjectList[] = $tagObject;
            }
        }
        return $tagObjectList;
    }

    /**
     * 引数のTagオブジェクトからTLC構造体を取り出し
     * tagListにpushする
     *
     * @access public
     * @param  Tag $tag Tagオブジェクト
     */
    public function add(ZSwf_Tag $tagObject)
    {
        $tagList[] = $tagPbject->pack();
    }

    /**
     * 引数のTagオブジェクトからTLC構造体を取り出し
     * tagList内にある該当するTagと入れ替える
     *
     * @access public
     * @param  array  $field   入れ替えるフィールドのリスト
     * @param  string $tagName 入れ替え対象となるタグ名
     * @param  array  $filter  入れ替え対象となるTagの条件。空の場合は同一タイプのTag全てと入れ替える。
     */
    public function replace(Array $fieldList, $tagName, Array $filter = array())
    {
        $className = 'Zswf_' . $tagName;
        for ($i = 0; $i < count($this->tagList); $i++) {
            if ($this->tagList[$i]['tag'] === $className::NUMBER) {
                $tagObject = new $className($this->tagList[$i]);
                //filter
                foreach ($filter as $key => $value) {
                    if ($tagObject->getField($key) !== $value) {
                        continue 2;
                    }
                }
                //入れ替え
                foreach ($fieldList as $key => $value) {
                    $tagObject->setField(array($key => $value));
                }
                $this->tagList[$i] = $tagObject->pack();
            }
        }
    }

    /**
     * Tag Blockをバイナリにパック
     *
     * @access public
     * @return string バイナリ
     */
    public function pack()
    {
        $tagBlock = '';
        foreach($this->tagList as $tag) {
            if ($tag['format'] === 0) {
                $recordHeader = pack('v', ($tag['tag'] << 6) | $tag['length']);
            } else {
                $recordHeader = pack('v', ($tag['tag'] << 6) | 0x3f); 
                $recordHeader .= pack('V', $tag['length']);
            }
            $tagBlock .= $recordHeader . $tag['contents'];
        }
        return $tagBlock;
    }

    /**
     * Tag Blockを最適化
     * Contents部のサイズを計算しTag Formatを求め、TLCを再構築する。
     *
     * @access public
     * @return string バイナリ
     */
    public function compact()
    {
        for ($i = 0; $i < count($this->tagList); $i++) {
            $this->tagList[$i]['length'] = strlen($this->tagList[$i]['contents']);
            if ($this->tagList[$i]['length'] >= 0x3f) {
                $this->tagList[$i]['format'] = 1;
            } else {
                $this->tagList[$i]['format'] = 0;
            }
        }
    }

    /**
     * Tag Blockのバイナリを展開
     * 各TLCを連想配列にして$tagListにpush
     *
     * @access public
     * @param  string $bin Tag Blockのバイナリデータ
     */
    public function unpack($bin)
    {
        ZSwf_Util_Byte::resetOffset();
        while (true) {
            $tag = array();
            $arr = unpack('v', ZSwf_Util_Byte::getBytes($bin, 2));
            $recordHeader = $arr[1];
            //上位10ビットがtag
            $tag['tag'] = $recordHeader >> 6;
            //残り6ビットがlength
            $tag['length'] = $recordHeader & 0x3f;
            $tag['format'] = 0;
            //ただし、0x3f以上の場合はlong format
            if ($tag['length'] >= 0x3f) {
                $arr = unpack('V', ZSwf_Util_Byte::getBytes($bin, 4));
                $tag['length'] = $arr[1];
                $tag['format'] = 1;
            }
            $tag['contents'] = ZSwf_Util_Byte::getBytes($bin, $tag['length']);
            //tag blockの追加
            $this->tagList[] = $tag;
            //end tag
            if ($tag['tag'] == 0) {
                break;
            }
        }
    }
}