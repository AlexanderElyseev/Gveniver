<?php
/**
 * File contains extension class for working with other extensions.
 *  
 * @category  Gveniver
 * @package   Extension
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Prof-Club
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

namespace Gveniver\Extension;
\Gveniver\Loader::i('system/extension/SimpleExtension.inc.php');
\Gveniver\Loader::i('system/extension/ExtensionReply.inc.php');

/**
 * Extension class for working with other extensions.
 * 
 * @category  Gveniver
 * @package   Extension
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Prof-Club
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class GvExtensionExt extends SimpleExtension
{
    /**
     * Executin query to external extension.
     *
     * @param string $sExt    Extension.
     * @param string $sAct    Action.
     * @param array  $aParams Parameters of call.
     * @param string $sFormat Output format.
     *
     * @return ExtensionReply
     */
    public function externalQuery($sExt = null, $sAct = null, array $aParams = array(), $sFormat = null)
    {
        $cReply = new ExtensionReply();
        
        // Extension and action must be specified.
        if (!$sExt || !$sAct) {
            $cReply->setStatus(false);
            $cReply->setMessage('Extension or action is not specified.');
            return $cReply;
        }

        // Load extension.
        $cExtension = $this->getApplication()->extension->getExtension($sExt);
        if (!$cExtension) {
            $cReply->setStatus(false);
            $cReply->setMessage('Extension is not loaded.');
            return $cReply;
            
        } // End if

        // Executing query.
        $cResult = $cExtension->query($sAct, $aParams, array('format' => $sFormat, 'external' => true));
        if (!$cResult instanceof ExtensionReply) {
            $cReply->setStatus(true);
            $cReply->setReply($cResult);
            return $cReply;
        }

        return $cResult;
        
    } // End function
    //-----------------------------------------------------------------------------

} // End class
//------------------------------------------------------------------------------------------