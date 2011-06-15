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

namespace Gveniver;
Loader::i('system/template/factory/BaseTemplateFactory.inc.php');

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
abstract class BaseFileTemplateFactory extends BaseTemplateFactory
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
     * Folder for template files.
     *
     * @var string
     */
    protected $sTplFolder;
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------

    /**
     * Class constructor.
     * Initialize member fields. Load parameters of template subsystem from configuration.
     *
     * @param Kernel\Kernel $cKernel Current kernel.
     *
     * @throws Exception
     */
    public function __construct(Kernel\Kernel $cKernel)
    {
        // Execute parent constructor.
        parent::__construct($cKernel);

        // Template files extension.
        $sExt = $this->cKernel->cConfig->get('Module/TemplateModule/Ext');
        if (!$sExt)
            throw new \Gveniver\Exception\Exception('Extension of template files not loaded from configuration.');
        $this->sTplFileNameExtension = ($sExt[0] != '.') ? '.'.$sExt : $sExt;

        // Template files separator.
        $this->sTplFileNameSeparator = $this->cKernel->cConfig->get(
            array('Module/TemplateModule/Separator')
        );
        if (!$this->sTplFileNameSeparator)
            throw new \Gveniver\Exception\Exception('Extension of template files not loaded from configuration.');

        // Template folder.
        $this->sTplFolder = $this->cKernel->cConfig->get('Profile/Path/AbsTemplate');
        if (!$this->sTplFolder  || !is_dir($this->sTplFolder) || !is_readable($this->sTplFolder))
            throw new \Gveniver\Exception\Exception('Wrong template directory.');
        
    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Returns the content of template file by template name.
     *
     * @param string $sTemplateName Template name for loading file content.
     * 
     * @return string|null Returns null if file not found.
     */
    protected function getTemplateFileContent($sTemplateName)
    {
        $sTemplateFileName = $this->getTemplateFileName($sTemplateName);
        if ($sTemplateFileName)
            return file_get_contents($this->sTplFolder.$sTemplateFileName);

        return null;
        
    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Returns correct template file name by template name.
     *
     * @param string $sName Template name for loading file name.
     *
     * @return string|null Returns null if file not found.
     */
    protected function getTemplateFileName($sName)
    {
        if (isset($this->_aNameCache[$sName])) {
            return $this->_aNameCache[$sName];
        } else {
            $sAbsFileName = $this->sTplFolder . $sName;
            $sAbsFileNameWithExt = $sAbsFileName . $this->sTplFileNameExtension;
            if (file_exists($sAbsFileNameWithExt) && !is_dir($sAbsFileNameWithExt))
                return $this->_aNameCache[$sName] = $sName . $this->sTplFileNameExtension;
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
                    $sComplexAbsFileName = $this->sTplFolder . $sName;
                    $sComplexAbsFileNameWithExt = $this->sTplFolder . $sName . $this->sTplFileNameExtension;

                    if (file_exists($sComplexAbsFileNameWithExt) && !is_dir($sComplexAbsFileNameWithExt))
                        return $this->_aNameCache[$sName] = $sComplexAbsFileNameWithExt;
                    elseif (file_exists($sComplexAbsFileName) && !is_dir($sComplexAbsFileName))
                        return $this->_aNameCache[$sName] = $sComplexAbsFileName;

                } // End for

                $this->cKernel->trace->addLine('[%s] Template ("%s") not found.', __CLASS__, $sStartName);

            } // End else

        } // End else

        return null;
        
    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------