<?php
/**
 * File contains class of simple kernel extension.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

namespace Gveniver\Extension;
\Gveniver\Loader::i('system/extension/Extension.inc.php');

/**
 * Class of simple kernel extension.
 * Without export data.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class SimpleExtension extends Extension
{
    /**
     * Query to extension.
     * Redirect request to public function of current extension with same name.
     *
     * @param string $sAction Name of action handler.
     * @param array  $aParams Arguments to extension.
     *
     * @return mixed
     */
    public function query($sAction, $aParams = array())
    {
        $this->cKernel->trace->addLine('[%s] Executing query: "%s".', __CLASS__, $sAction);

        // Check existence of method.
        if (!method_exists($this, $sAction)) {
            $this->cKernel->trace->addLine('[%s] Handler for query ("%s") is not found.', __CLASS__, $sAction);
            return null;
        }

        // Method must be public.
        $cRefl = new \ReflectionMethod($this, $sAction);
        if (!$cRefl->isPublic()) {
            $this->cKernel->trace->addLine('[%s] Handler for query ("%s") is not public.', __CLASS__, $sAction);
            return null;
        }
        
        $this->cKernel->trace->addLine('[%s] Handler found for query: "%s".', __CLASS__, $sAction);
        
        return call_user_func_array(array($this, $sAction), $aParams);

    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------