<?php
/**
 * File contains class of simple extension.
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
 * Class of simple extension.
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
     * @param string $sFormat Output format name.
     *
     * @return mixed
     */
    public function query($sAction, $aParams = array(), $sFormat = null)
    {
        $this->getApplication()->trace->addLine('[%s] Executing query: "%s".', __CLASS__, $sAction);

        // Load name of method by specified output format.
        $sMethodName = null;
        if ($sFormat) {
            $aActList = $this->getConfig()->get('Extension/ActList');
            if (is_array($aActList)) {
                foreach ($this->getConfig()->get('Extension/ActList') as $aAction) {
                    if (isset($aAction['Name']) && $aAction['Name'] == $sAction && isset($aAction['FormatList'])) {
                        foreach ($aAction['FormatList'] as $aFormat) {
                            if (isset($aFormat['Method']) && isset($aFormat['Name']) && $aFormat['Name'] == $sFormat) {
                                $sMethodName = $aFormat['Method'];
                                break 2;
                            }
                        }
                    }
                }
            }

            if (!$sMethodName) {
                $this->getApplication()->trace->addLine(
                    '[%s] Handler for query ("%s") and format ("%s") is not found.',
                    __CLASS__,
                    $sAction,
                    $sFormat
                );
                return null;
            }

        } else
            $sMethodName = $sAction;

        // Check existence of method.
        if (!method_exists($this, $sMethodName)) {
            $this->getApplication()->trace->addLine('[%s] Handler for query ("%s") is not found.', __CLASS__, $sAction);
            return null;
        }

        // Method must be public.
        $cRefl = new \ReflectionMethod($this, $sMethodName);
        if (!$cRefl->isPublic()) {
            $this->getApplication()->trace->addLine('[%s] Handler for query ("%s") is not public.', __CLASS__, $sAction);
            return null;
        }
        
        $this->getApplication()->trace->addLine('[%s] Handler found for query: "%s".', __CLASS__, $sAction);
        
        return call_user_func_array(array($this, $sMethodName), $aParams);

    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------