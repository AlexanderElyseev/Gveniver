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
     * @param string $sExtensionName Extension.
     * @param string $sActionValue   Action.
     * @param array  $aArguments     Parameters of call.
     * @param string $sFormat        Output format.
     *
     * @return ExtensionReply
     */
    public function externalQuery($sExtensionName, $sActionValue, $aArguments = array(), $sFormat = null)
    {
        if (!$aArguments)
            $aArguments = array();

        $cReply = new ExtensionReply();
        
        // Extension and action must be specified.
        if (!$sExtensionName || !$sActionValue) {
            $cReply->setStatus(false);
            $cReply->setMessage('Extension or action is not specified.');
            return $cReply;
        }

        // Load extension.
        $cExtension = $this->getApplication()->extension->getExtension($sExtensionName);
        if (!$cExtension) {
            $cReply->setStatus(false);
            $cReply->setMessage('Extension is not loaded.');
            return $cReply;
            
        } // End if

        /* @var $cExtension Extension */

        // Executing query.
        $cResult = $cExtension->query(
            $sActionValue,
            $aArguments,
            array(
                'format'   => $sFormat,
                'external' => true
            )
        );
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