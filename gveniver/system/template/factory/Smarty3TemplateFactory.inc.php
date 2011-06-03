<?php
/**
 * File contains template factory class for Smarty template system.
 *
 * @category  Gveniver
 * @package   Template
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

GvInclude::instance()->includeFile('system/template/factory/BaseFileTemplateFactory.inc.php');
GvInclude::instance()->includeFile('system/template/Smarty3Template.inc.php');

/**
 * Template factory class for Smarty template system.
 *
 * @category  Gveniver
 * @package   Template
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class Smarty3TemplateFactory extends BaseFileTemplateFactory
{
    /**
     * Smarty object.
     *
     * @var Smarty
     */
    private $_cSmarty;
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------

    /**
     * Class constructor. Initialize Smarty system.
     *
     * @param GvKernel $cKernel Current kernel.
     * 
     * @throws GvException
     */
    public function __construct(GvKernel $cKernel)
    {
        // Use base constructor.
        parent::__construct($cKernel);

        // Check, is SMarty is exists on system.
        if (!class_exists('Smarty'))
            throw new GvException('Smarty is not installed.');
        
        // Initialize smarty.
        $this->_cSmarty = new Smarty();
        if (!$this->_reinstallSmarty())
            throw new GvException('Smarty configuration failed.');

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Reinitialize smarty by configuration parameters.
     * 
     * @return bool True on success.
     */
    private function _reinstallSmarty()
    {
        $sDirTemplate = $this->cKernel->cConfig->get('Module/TemplateModule/SmartyDirTemplate');
        if (!$this->_createDir($sDirTemplate) || !is_readable($sDirTemplate))
            return false;
        $this->_cSmarty->template_dir = $sDirTemplate;

        $sDirCompile = $this->cKernel->cConfig->get('Module/TemplateModule/SmartyDirCompile');
        if (!$this->_createDir($sDirCompile) || !is_writable($sDirCompile))
            return false;
        $this->_cSmarty->compile_dir = $sDirCompile;

        $sDirCache = $this->cKernel->cConfig->get('Module/TemplateModule/SmartyDirCache');
        if (!$this->_createDir($sDirCache) || !is_writable($sDirCache))
            return false;
        $this->_cSmarty->cache_dir = $sDirCache;

        $sDirConfig = $this->cKernel->cConfig->get('Module/TemplateModule/SmartyDirConfig');
        if (!$this->_createDir($sDirConfig) || !is_readable($sDirConfig))
            return false;
        $this->_cSmarty->config_dir = $sDirConfig;

        $this->_cSmarty->left_delimiter = $this->cKernel->cConfig->get('Module/TemplateModule/DelimiterBegin');
        $this->_cSmarty->right_delimiter = $this->cKernel->cConfig->get('Module/TemplateModule/DelimiterEnd');
        $this->_cSmarty->registerPlugin('function', 'ext', array($this, 'extension'));
        $this->_cSmarty->registerPlugin('modifier', 'upper', 'strtoupper_ex');
        $this->_cSmarty->registerPlugin('modifier', 'lower', 'strtolower_ex');
        $this->_cSmarty->registerPlugin('modifier', 'substr', 'mb_substr');
        return true;
        
    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Function for check existence and create directory.
     *
     * @param string $sPath path for create directory.
     *
     * @return bool True if directory is exists.
     */
    private function _createDir($sPath)
    {
        if (!$sPath)
            return false;

        if (file_exists($sPath)) {
            if (is_dir($sPath))
                return true;
            else
                return false;
        }

        return mkdir($sPath, 0666, true);
        
    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Build template by template name.
     * Must implements specific actions for specific template types.
     *
     * @param string $sTemplateName Name of template for building.
     *
     * @return BaseTemplate|null
     */
    protected function build($sTemplateName)
    {
        $sTemplateFile = $this->getTemplateFileName($sTemplateName);
        if ($sTemplateFile)
            return new Smarty3Template($this->_cSmarty, $sTemplateFile);

        return null;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Smarty plugin function for executing query to extension from templates.
     *
     * @param array  $aParams  Parameters.
     * @param Smarty &$cSmarty Smarty object.
     *
     * @return string Result of extension call.
     */
    public function extension($aParams, &$cSmarty)
    {
        $cExtModule = $this->cKernel->extension;
        if (!$cExtModule) {
            $this->cKernel->trace->addLine('[%s] Extension module not found for Smarty query.', __CLASS__);
            return null;
        }

        // Build parameters for extension query.
        $sExtensionName = isset($aParams['ext']) ? $aParams['ext'] : null;
        $sExtensionHandlerName = isset($aParams['act']) ? $aParams['act'] : null;
        $sVarName = isset($aParams['var']) ? $aParams['var'] : null;
        if (!$sExtensionName || !$sExtensionHandlerName) {
            $this->cKernel->trace->addLine('[%s] Wrong arguments at extension query from Smarty.', __CLASS__);
            return null;
        }

        // Load extension.
        $cExt = $cExtModule->getExtension($sExtensionName);
        if (!$cExt) {
            $this->cKernel->trace->addLine(
                '[%s] Extension ("%s") not found for Smarty query.',
                __CLASS__,
                $sExtensionName
            );
            return null;
        }

        // Executing query.
        unset($aParams['ext']);
        unset($aParams['act']);
        unset($aParams['var']);
        $sRet = $cExt->query($sExtensionHandlerName, $aParams);

        // Assign result to specified variable.
        if ($sVarName) {
            $cSmarty->assign($sVarName, $sRet);
            return "";
        }
        return $sRet;

    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------