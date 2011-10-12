<?php
/**
 * File contains base system exception class.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

namespace Gveniver\Exception;

/**
 * Base system exception class.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class BaseException extends \Exception
{
    /**
     * Class constructor.
     * 
     * @param string $sMessage Error sMessage.
     * @param int    $nCode    Error nCode.
     */
    public function __construct($sMessage = '', $nCode = 0)
    {
        parent::__construct($sMessage, $nCode);
        
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
            '[%s] Exception. Message="%s", Code="%d".',
            __CLASS__,
            $this->message,
            $this->code
        );
        
    } // End function
    //-----------------------------------------------------------------------------
    
} // End class
//-----------------------------------------------------------------------------