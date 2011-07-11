<?php
/**
 * File contains abstraction class for complex reply of extension to some query.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

namespace Gveniver\Extension;

/**
 * Abstraction class for complex reply of extension to some query.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 * @abstract
 */
class ExtensionReply
{
    /**
     * Result of query.
     *
     * @var mixed
     */
    private $_mReply;
    //-----------------------------------------------------------------------------

    /**
     * Result status of query.
     * 
     * @var bool
     */
    private $_bStatus;
    //-----------------------------------------------------------------------------

    /**
     * Message for users.
     *
     * @var string
     */
    private $_sMessage;
    //-----------------------------------------------------------------------------

    /**
     * Result code of query.
     *
     * @var int
     */
    private $_nCode;
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------

    /**
     * Class constructor.
     *
     * @param string $sReply   Result of query.
     * @param bool   $bStatus  Result status of query.
     * @param string $sMessage Message for users.
     * @param int    $nCode    Result code of query.
     */
    public function __construct($sReply = '', $bStatus = false, $sMessage = '', $nCode = null)
    {
        $this->_mReply = $sReply;
        $this->_bStatus = $bStatus;
        $this->_sMessage = $sMessage;
        $this->_nCode = $nCode;
        
    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Converter current instance to string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getReply();
        
    } // End function
    //-----------------------------------------------------------------------------
    
    /**
     * Getter for result reply of query.
     *
     * @return mixed
     */
    public function getReply()
    {
        return $this->_mReply;
        
    } // End function
    //-----------------------------------------------------------------------------
    
    /**
     * Setter for reply of extension.
     * 
     * @param mixed $mReply Result of query to set.
     * 
     * @return void
     */
    public function setReply($mReply)
    {
        $this->_mReply = $mReply;
        
    } // End function
    //-----------------------------------------------------------------------------
    
    /**
     * Getter for result status of query.
     * 
     * @return boolean
     */
    public function getStatus()
    {
        return $this->_bStatus;
        
    } // End function
    //-----------------------------------------------------------------------------
    
    /**
     * Setter for result status of query.
     * 
     * @param boolean $bStatus Tesult status of query to set.
     * 
     * @return void
     */
    public function setStatus($bStatus)
    {
        $this->_bStatus = (boolean)$bStatus;
        
    } // End function
    //-----------------------------------------------------------------------------
    
    /**
     * Getter for message to users.
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->_sMessage;
        
    } // End function
    //-----------------------------------------------------------------------------
    
    /**
     * Setter for message to users.
     *
     * @param string $sMessage Message text to set.
     * 
     * @return void
     */
    public function setMessage($sMessage)
    {
        $this->_sMessage = $sMessage;
        
    } // End function
    //-----------------------------------------------------------------------------
    
    /**
     * Getter for result code of query.
     * 
     * @return int
     */
    public function getCode()
    {
        return $this->_nCode;
        
    } // End function
    //-----------------------------------------------------------------------------
    
    /**
     * Setter for result code of query.
     * 
     * @param int $nCode Result code to set.
     * 
     * @return void
     */
    public function setCode($nCode)
    {
        $this->_nCode = $nCode;
        
    } // End function
    //-----------------------------------------------------------------------------
    
} // End class
//-----------------------------------------------------------------------------