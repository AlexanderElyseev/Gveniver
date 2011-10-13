<?php
/**
 * File contains class of extension module.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

namespace Gveniver\Kernel\Module;

/**
 * Class of extension module.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class ExtensionModule extends BaseModule
{
    /**
     * Current loader for extensions.
     *
     * @var \Gveniver\Extension\Loader\BaseExtensionLoader
     */
    private $_cLoader;
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------

    /**
     * Full initialization of module.
     *
     * @return bool True on success.
     */
    protected function init()
    {
        $this->getApplication()->trace->addLine('[%s] Init.', __CLASS__);

        // Try to create extension loader.
        $this->_cLoader = new \Gveniver\Extension\Loader\DirectoryExtensionLoader($this->getApplication());
        if (!$this->_cLoader) {
             $this->getApplication()->trace->addLine(
                 '[%s] Error in create extension loader.'
             );
            return false;
        }

        $this->getApplication()->trace->addLine('[%s] Init successful.', __CLASS__);
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
     * @return ExtensionModule|bool
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