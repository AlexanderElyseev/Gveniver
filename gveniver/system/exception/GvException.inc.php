<?php
/**
 *
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

/**
 *
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class GvException extends Exception
{
    /**
     * Class constructor.
     * 
     * @param string $message Error message.
     * @param int    $code    Error code.
     * 
     * @return void
     */
    public function __construct($message = '', $code = 0) 
    {
        parent::__construct($message, $code);
        
    } // End function
    //-----------------------------------------------------------------------------
    
    /**
     * Convert exception to string.
     * 
     * @return string
     */
    public function __toString() 
    {
        return sprintf(
            '[%s] Exception. Message="%s", code="%d".',
            __CLASS__,
            $this->message,
            $this->code
        );
        
    } // End function
    //-----------------------------------------------------------------------------
    
} // End class
//-----------------------------------------------------------------------------