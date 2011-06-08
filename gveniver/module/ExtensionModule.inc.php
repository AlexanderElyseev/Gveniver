<?php
/**
 * File contains class of extension kernel module.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

GvInclude::i('GvKernelModule.inc.php');

/**
 * Class of extension kernel module.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class ExtensionModule extends GvKernelModule
{
    /**
     * Current loader for extensions.
     *
     * @var ExtensionLoader
     */
    private $_cLoader;
    //-----------------------------------------------------------------------------

    /**
     * Full initialization of kernel module.
     *
     * @return bool True on success.
     */
    protected function init()
    {
        $this->cKernel->trace->addLine('[%s] Init.', __CLASS__);

        // Try to create extension loader.
        $this->_cLoader = GvInclude::createObject(
            array(
                'class' => 'DirectoryExtensionLoader',
                'path'  => 'system/extension/loader/%class%.inc.php',
                'args'  => array($this->cKernel)
            ),
            $nErrCode
        );
        if (!$this->_cLoader) {
             $this->cKernel->trace->addLine(
                 '[%s] Error in create extension loader, with code: %d.',
                 __CLASS__,
                 $nErrCode
             );
            return false;
        }

        $this->cKernel->trace->addLine('[%s] Init sucessful.', __CLASS__);
        return true;
        
    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Loading extension by name.
     *
     * @param string $sExtensionName Name of extension.
     * @param mixed  &$cRef          Reference variable for loading extension.
     * If specified, then the extension loads to variable by refernce and returns
     * result of operation (boolean). Otherwise, returns template.
     *
     * @return GvKernelExtension|bool
     */
    public function getExtension($sExtensionName, &$cRef = null)
    {
        $cExt = $this->_cLoader->get($sExtensionName);

        // Return resul by reference.
        if (func_num_args() > 1) {
            if (!$cExt)
                return false;

            $cRef = $cExt;
            return true;
        }

        return $cExt;

    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------