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

GvKernelInclude::instance()->includeFile('src/system/extension/loader/ExtensionLoader.inc.php');

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
class FolderExtensionLoader extends ExtensionLoader
{
    /**
     * Path to extension folder.
     * 
     * @var string
     */
    protected $sExtensionFolder;
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------

    /**
     * Class constructor.
     * Initialize member fields.
     *
     * @param GvKernel $cKernel Current kernel.
     *
     * @throws GvException
     */
    public function __construct(GvKernel $cKernel)
    {
        // Execute parent constructor.
        parent::__construct($cKernel);

        // Load extension folder.
        $this->sExtensionFolder = (string)$this->cKernel->cConfig->get('Profile/Path/AbsExtension');
        if (!$this->sExtensionFolder || !file_exists($this->sExtensionFolder) || !is_dir($this->sExtensionFolder))
            throw new GvException('Wrong extension directory.');

    } // End function
    //-----------------------------------------------------------------------------
    
    /**
     * Template method for direct loading of extension by name.
     * Dynamically load extension object.
     *
     * @param string $sExtensionName Name of extension for loading.
     *
     * @return GvKernelExtension Returns null on error.
     */
    protected function load($sExtensionName)
    {
        // Build extension class name.
		$sExtensionClassName = $sExtensionName;
        $sExtensionFileName = $this->sExtensionFolder.$sExtensionClassName.GV_DS.$sExtensionClassName.'.inc.php';

        $this->cKernel->trace->addLine(
            '[%s] Loading extension ("%s") with class name: "%s".',
            __CLASS__,
            $sExtensionName,
            $sExtensionClassName
        );

        // Dynamically load extension.
        $cExt = GvKernelInclude::createObject(
            array(
                'class' => $sExtensionClassName,
                'path'  => $sExtensionFileName,
                'args'  => array($this->cKernel)
            ),
            $nErrCode
        );
        if ($cExt) {
            $this->cKernel->trace->addLine(
                '[%s] Extension ("%s") successfully loaded.',
                __CLASS__,
                $sExtensionName
            );

            return $cExt;
        }

        $this->cKernel->trace->addLine(
            '[%s] Extension ("%s") Not loaded. Error code: %d.',
            __CLASS__,
            $sExtensionName,
            $nErrCode
        );
        
        return null;

    } // End function
    //-----------------------------------------------------------------------------
    
} // End class
//-----------------------------------------------------------------------------