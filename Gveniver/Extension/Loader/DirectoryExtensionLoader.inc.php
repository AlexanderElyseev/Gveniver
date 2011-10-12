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

namespace Gveniver\Extension\Loader;

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
class DirectoryExtensionLoader extends BaseExtensionLoader
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
    private $_aExtensionDirList = array();
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
        $this->_registerExtDir(GV_PATH_BASE.'Extension'.GV_DS);

        // Register extension directory from profile configuration.
        $this->_registerExtDir($this->getApplication()->getConfig()->get('Profile/Path/AbsExtension'));

        // Load caching settings.
        $this->_bForceExportCache = \Gveniver\Kernel\Application::toBoolean(
            $this->getApplication()->getConfig()->get('Kernel/EnableCache')
        );
        
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
            $this->getApplication()->trace->addLine(
                '[%s] Wrong extension directory argument.',
                __CLASS__,
                $sDir
            );
            return;
        }

        // Check, is path already exists.
        if (in_array($sDir, $this->_aExtensionDirList)) {
            $this->getApplication()->trace->addLine(
                '[%s] Extension directory ("%s") already registered.',
                __CLASS__,
                $sDir
            );
            return;
        }

        // Check directory and register.
        if ($sDir && file_exists($sDir) && is_dir($sDir) && is_readable($sDir)) {

            $this->getApplication()->trace->addLine('[%s] Extension directory ("%s") registered.',  __CLASS__, $sDir);
            array_unshift($this->_aExtensionDirList, $sDir);

        } else {
            $this->getApplication()->trace->addLine('[%s] Wrong extension directory ("%s").',  __CLASS__, $sDir);

        }
        
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
        $sExtensionClassName = '\\Gveniver\\Extension\\'.$sExtensionName;

        $bNeedInclude = !class_exists($sExtensionClassName, false);

        // Load extension in registered directories.
        foreach ($this->_aExtensionDirList as $sExtensionDir) {

            $sExtensionDir = $sExtensionDir.$sExtensionName.GV_DS;

            if ($bNeedInclude) {
                $sExtensionFileName = $sExtensionDir.$sExtensionName.'.inc.php';
                $this->getApplication()->trace->addLine(
                    '[%s] Loading extension ("%s") in "%s".',
                    __CLASS__,
                    $sExtensionName,
                    $sExtensionFileName
                );

                // File with class must exists.
                if (!file_exists($sExtensionFileName)) {
                    $this->getApplication()->trace->addLine(
                        '[%s] Extension file "%s" not exists.',
                        __CLASS__,
                        $sExtensionFileName
                    );
                    continue;
                }

                /** @noinspection PhpIncludeInspection */
                include_once $sExtensionFileName;

                // After including, class must exists.
                $bNeedInclude = !class_exists($sExtensionClassName, false);
                if ($bNeedInclude) {
                    $this->getApplication()->trace->addLine(
                        '[%s] Extension "%s" not found in "%s".',
                        __CLASS__,
                        $sExtensionName,
                        $sExtensionFileName
                    );
                    continue;
                }
            }
            
            $this->getApplication()->trace->addLine(
                '[%s] Loading extension "%s".',
                __CLASS__,
                $sExtensionName
            );

            // Dynamically load extension.
            return $this->_buildExtension($sExtensionClassName, $sExtensionDir);
            
        } // End foreach

        $this->getApplication()->trace->addLine('[%s] Extension ("%s") not loaded.', __CLASS__, $sExtensionName);
        return null;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Build extension instance.
     * 
     * @param string $sExtensionClassName Class name of extension.
     * @param string $sExtensionDirectory Directory with extension.
     * 
     * @return \Gveniver\Extension\BaseExtension|null
     */
    private function _buildExtension($sExtensionClassName, $sExtensionDirectory)
    {
        try {
            $cExtension = new $sExtensionClassName($this->getApplication());

            /* @var $cExtension \Gveniver\Extension\BaseExtension */

            // Load extension configuration.
            $sExtensionExportFileName = $sExtensionDirectory.'export.xml';
            if (file_exists($sExtensionExportFileName) && is_readable($sExtensionExportFileName))
                $cExtension->getConfig()->mergeXmlFile($sExtensionExportFileName, $this->_bForceExportCache);

            $this->getApplication()->trace->addLine(
                '[%s] Extension ("%s") successfully loaded.',
                __CLASS__,
                $sExtensionClassName
            );
            return $cExtension;

        } catch (\Exception $cEx) {
            $this->getApplication()->trace->addLine(
                '[%s] Extension ("%s") not loaded loaded: "%s".',
                __CLASS__,
                $sExtensionClassName,
                $cEx->getMessage()
            );
        }

        return null;

    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------