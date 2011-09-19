<?php
/**
 * File contains base abstract template factory class.
 *
 * @category  Gveniver
 * @package   Template
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

namespace Gveniver\Template;
\Gveniver\Loader::i('system/template/factory/TemplateFactory.inc.php');

/**
 * Base abstract template factory class.
 *
 * @category  Gveniver
 * @package   Template
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 * @abstract
 */
abstract class FileTemplateFactory extends TemplateFactory
{
    /**
     * Cache of template file names.
     *
     * @var array
     */
    private $_aNameCache = array();
    //-----------------------------------------------------------------------------

    /**
     * Extension for template files.
     *
     * @var string
     */
    protected $sTplFileNameExtension;
    //-----------------------------------------------------------------------------

    /**
     * Template file name seprarator.
     *
     * @var string
     */
    protected $sTplFileNameSeparator;
    //-----------------------------------------------------------------------------

    /**
     * Directories for template files.
     *
     * @var array
     */
    protected $aTplDirectories = array();
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------

    /**
     * Class constructor.
     * Initialize member fields. Load parameters of template subsystem from configuration.
     *
     * @param \Gveniver\Kernel\Application $cApplication Current application.
     *
     * @throws \Gveniver\Exception\Exception Throws if directory with templates not loaded
     * correctly.
     */
    public function __construct(\Gveniver\Kernel\Application $cApplication)
    {
        // Execute parent constructor.
        parent::__construct($cApplication);

        // Template files extension.
        $sExt = $this->getApplication()->getConfig()->get('Module/TemplateModule/Ext');
        if (!$sExt)
            throw new \Gveniver\Exception\Exception('Extension of template files not loaded from configuration.');
        $this->sTplFileNameExtension = ($sExt[0] != '.') ? '.'.$sExt : $sExt;

        // Template files separator.
        $this->sTplFileNameSeparator = $this->getApplication()->getConfig()->get(
            array('Module/TemplateModule/Separator')
        );
        if (!$this->sTplFileNameSeparator)
            throw new \Gveniver\Exception\Exception('Extension of template files not loaded from configuration.');

        // Template folders.
        $cP = $this->getApplication()->getProfile();
        do {
            $sTplDirectory = $cP->getConfig()->get('Profile/Path/AbsTemplate');
            if (!$sTplDirectory  || !is_dir($sTplDirectory) || !is_readable($sTplDirectory)) {
                $cApplication->trace->addLine(
                    '[%s] wrong template directory of profile ("%s").',
                    __CLASS__,
                    $sTplDirectory
                );
            }

            $this->aTplDirectories[] = $sTplDirectory;
            
            if ($cP->getParentProfile())
                $cP = $cP->getParentProfile();
            else
                break;

        } while (true);

        if (!count($this->aTplDirectories))
            throw new \Gveniver\Exception\Exception('Wrong template directory.');


    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Returns correct template file path by template name.
     *
     * @param string $sName Template name for loading file name.
     *
     * @return string|null Returns null if file not found.
     */
    protected function getTemplateFileName($sName)
    {
        // Load from cache.
        if (isset($this->_aNameCache[$sName]))
            return $this->_aNameCache[$sName];

        // Search in all template directories.
        foreach ($this->aTplDirectories as $sTplDirectory) {

            $sAbsFileName = $sTplDirectory.$sName;
            $sAbsFileNameWithExt = $sAbsFileName.$this->sTplFileNameExtension;
            if (file_exists($sAbsFileNameWithExt) && !is_dir($sAbsFileNameWithExt))
                return $this->_aNameCache[$sName] = $sName.$this->sTplFileNameExtension;
            elseif (file_exists($sAbsFileName) && !is_dir($sAbsFileName))
                return $this->_aNameCache[$sName] = $sName;
            else {
                // Load template content by complex file name from base folder.
                // a_b_c_d.tpl ---> a/b_c_d.tpl ---> ... ---> a/b/c/d.tpl
                $sStartName = $sName;
                $nLength = mb_strlen($sName);
                for ($i = 0; $i < $nLength; $i++) {
                    if ($sName[$i] !== $this->sTplFileNameSeparator)
                        continue;

                    // Replace with separator.
                    $sName[$i] = GV_DS;

                    // Try to load template content.
                    $sComplexAbsFileName = $sTplDirectory.$sName;
                    $sComplexAbsFileNameWithExt = $sTplDirectory.$sName . $this->sTplFileNameExtension;

                    if (file_exists($sComplexAbsFileNameWithExt) && !is_dir($sComplexAbsFileNameWithExt))
                        return $this->_aNameCache[$sName] = $sComplexAbsFileNameWithExt;
                    elseif (file_exists($sComplexAbsFileName) && !is_dir($sComplexAbsFileName))
                        return $this->_aNameCache[$sName] = $sComplexAbsFileName;

                } // End for

                $this->getApplication()->trace->addLine('[%s] Template ("%s") not found.', __CLASS__, $sStartName);

            } // End else

        } // End foreach

        return null;
        
    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------