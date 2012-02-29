<?php
/**
 * Exception.php
 *
 * @author   Shotaro Emori
 * @package  ZSwf
 */

/**
 * ZSwf_Exception Class
 *
 * @author   Shotaro Emori
 * @package  ZSwf
 * @access   public
 */
class ZSwf_Exception extends Exception
{
    /**
     * constructer
     *
     * @access public
     * @param  string  $message
     * @param  integer $code
     * @return void
     */
    public function __construct($message, $code = 0)
    {
        parent::__construct(__CLASS__ . ': ' .$message, $code);
    }
}