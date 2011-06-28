<?php
/**
 * File contains loader class for extensions.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

namespace Gveniver\Extension;
\Gveniver\Loader::i('system/extension/loader/ExtensionLoader.inc.php');

/**
 * Loader class for extensions.
 * 
 * Implements logic of loading extensions from specified directories.
 * By default, load from extension directory and from directory
 * at profile configuration.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class DirectoryExtensionLoader extends ExtensionLoader
{
    /**
     * Force caching of export data.
     *
     * @var boolean
     */
    private $_bForceExportCache;
    //-----------------------------------------------------------------------------

    /**
     * Path to extension folder.
     * 
     * @var string
     */
    private $_aExtensionFolderList = array();
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------

    /**
     * Class constructor.
     * Register directories with extensions: by profile configuration and
     * base kernel extensiosn.
     *
     * @param \Gveniver\Kernel\Application $cApplication Current application.
     *
     * @throws Exception
     */
    public function __construct(\Gveniver\Kernel\Application $cApplication)
    {
        // Execute parent constructor.
        parent::__construct($cApplication);

        // Register extension directory.
        $this->_registerExtDir(GV_PATH_BASE.'extension'.GV_DS);

        // Register extension directory from profile configuration.
        $this->_registerExtDir($this->getApplication()->getConfig()->get('Profile/Path/AbsExtension'));

        // Load caching settings.
        $this->_bForceExportCache = \Gveniver\Kernel\Application::toBoolean($this->getApplication()->getConfig()->get('Kernel/EnableCache'));
        
    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Register directory with extensions.
     *
     * @param string $sDir Full path to directory with extensions.
     * 
     * @return void
     */
    private function _registerExtDir($sDir)
    {
        // Directory path must be string.
        if (!is_string($sDir)) {
            $this->getApplication()->trace->addLine('[%s] Wrong extension directory argument.',  __CLASS__, $sDir);
            return;
        }

        if (in_array($sDir, $this->_aExtensionFolderList)) {
            $this->getApplication()->trace->addLine('[%s] Extension directory ("%s") already registered.',  __CLASS__, $sDir);
            return;
        }

        // Check directory and register.
        if ($sDir && file_exists($sDir) && is_dir($sDir) && is_readable($sDir)) {
            $this->getApplication()->trace->addLine('[%s] Extension directory ("%s") registered.',  __CLASS__, $sDir);
            array_unshift($this->_aExtensionFolderList, $sDir);

        } else {
            $this->getApplication()->trace->addLine('[%s] Wrong extension directory ("%s").',  __CLASS__, $sDir);

        } // End else
        
    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Template method for direct loading of extension by name.
     * Dynamically load extension object.
     *
     * @param string $sExtensionName Name of extension for loading.
     *
     * @return Extension Returns null on error.
     */
    protected function load($sExtensionName)
    {
        // Load extension in registered directories.
        foreach ($this->_aExtensionFolderList as $sExtensionFolder) {
            // Build extension class name.
            $sExtensionClassName = $sExtensionName;
            $sExtensionFileName = $sExtensionFolder.$sExtensionClassName.GV_DS.$sExtensionClassName.'.inc.php';

            $this->getApplication()->trace->addLine('[%s] Loading extension ("%s") in "%s".', __CLASS__, $sExtensionName, $sExtensionFileName);

            // Dynamically load extension.
            $cExt = \Gveniver\Loader::createObject(
                array(
                    'class' => $sExtensionClassName,
                    'ns'    => '\\Gveniver\\Extension',
                    'path'  => $sExtensionFileName,
                    'args'  => array($this->getApplication())
                ),
                $nErrCode
            );
            if (!$cExt) {
                $this->getApplication()->trace->addLine(
                    '[%s] Extension ("%s") not loaded loaded at "%s". Error code: %d.',
                    __CLASS__,
                    $sExtensionName,
                    $sExtensionFileName,
                    $nErrCode
                );
                continue;
            }

            // Load extension configuration.
            $sExtensionExportFileName = $sExtensionFolder.$sExtensionClassName.GV_DS.'export.xml';
            if (file_exists($sExtensionExportFileName) && is_readable($sExtensionExportFileName))
                $cExt->getConfig()->mergeXmlFile($sExtensionExportFileName, $this->_bForceExportCache);
            
            $this->getApplication()->trace->addLine('[%s] Extension ("%s") successfully loaded.', __CLASS__, $sExtensionName);
            return $cExt;

        } // End foreach
 
        $this->getApplication()->trace->addLine('[%s] Extension ("%s") not loaded.', __CLASS__, $sExtensionName);

    } // End function
    //-----------------------------------------------------------------------------
    
} // End class
//-----------------------------------------------------------------------------