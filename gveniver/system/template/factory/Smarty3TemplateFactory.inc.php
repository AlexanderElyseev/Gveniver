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

namespace Gveniver\Template;
\Gveniver\Loader::i('system/template/factory/FileTemplateFactory.inc.php');
\Gveniver\Loader::i('system/template/Smarty3Template.inc.php');

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
class Smarty3TemplateFactory extends FileTemplateFactory
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
     * @param \Gveniver\Kernel\Application $cApplication Current application.
     * 
     * @throws \Gveniver\Exception\Exception Throws if Smarty library not loaded or
     * loaded incorrectly.
     */
    public function __construct(\Gveniver\Kernel\Application $cApplication)
    {
        // Use base constructor.
        parent::__construct($cApplication);

        // Check, is SMarty is exists on system.
        if (!class_exists('Smarty'))
            throw new \Gveniver\Exception\Exception('Smarty is not installed.');
        
        // Initialize smarty.
        $this->_cSmarty = new \Smarty();
        if (!$this->_reinstallSmarty())
            throw new \Gveniver\Exception\Exception('Smarty configuration failed.');

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Reinitialize smarty by configuration parameters.
     * 
     * @return bool True on success.
     */
    private function _reinstallSmarty()
    {
        $sDirTemplate = $this->getApplication()->getConfig()->get('Module/TemplateModule/SmartyDirTemplate');
        if (!$this->_createDir($sDirTemplate) || !is_readable($sDirTemplate))
            return false;
        $this->_cSmarty->template_dir = $sDirTemplate;

        $sDirCompile = $this->getApplication()->getConfig()->get('Module/TemplateModule/SmartyDirCompile');
        if (!$this->_createDir($sDirCompile) || !is_writable($sDirCompile))
            return false;
        $this->_cSmarty->compile_dir = $sDirCompile;

        $sDirCache = $this->getApplication()->getConfig()->get('Module/TemplateModule/SmartyDirCache');
        if (!$this->_createDir($sDirCache) || !is_writable($sDirCache))
            return false;
        $this->_cSmarty->cache_dir = $sDirCache;

        $sDirConfig = $this->getApplication()->getConfig()->get('Module/TemplateModule/SmartyDirConfig');
        if (!$this->_createDir($sDirConfig) || !is_readable($sDirConfig))
            return false;
        $this->_cSmarty->config_dir = $sDirConfig;

        $this->_cSmarty->left_delimiter = $this->getApplication()->getConfig()->get('Module/TemplateModule/DelimiterBegin');
        $this->_cSmarty->right_delimiter = $this->getApplication()->getConfig()->get('Module/TemplateModule/DelimiterEnd');
        $this->_cSmarty->registerPlugin('function', 'gv', array($this, 'extension'));
        $this->_cSmarty->registerPlugin('modifier', 'upper', '\\Gveniver\\strtoupper_ex');
        $this->_cSmarty->registerPlugin('modifier', 'lower', '\\Gveniver\\strtolower_ex');
        $this->_cSmarty->registerPlugin('modifier', 'substr', 'mb_substr');
        $this->_cSmarty->error_reporting = ini_get('error_reporting');
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
     * @return Template|null
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
        $aCopyParams = $aParams;

        $cExtModule = $this->getApplication()->extension;
        if (!$cExtModule) {
            $this->getApplication()->trace->addLine('[%s] Extension module not found for Smarty query.', __CLASS__);
            return null;
        }

        // Build parameters for extension query.
        $sExtensionName = isset($aParams['ext']) ? $aParams['ext'] : null;
        $sExtensionHandlerName = isset($aParams['act']) ? $aParams['act'] : null;
        $sVarName = isset($aParams['var']) ? $aParams['var'] : null;
        $sFormat = isset($aParams['format']) ? $aParams['format'] : null;
        $mCache = isset($aParams['cache']) ? $aParams['cache'] : null;
        if (!$sExtensionName || !$sExtensionHandlerName) {
            $this->getApplication()->trace->addLine('[%s] Wrong arguments at extension query from Smarty.', __CLASS__);
            return null;
        }
        
        // Load extension.
        $cExt = $cExtModule->getExtension($sExtensionName);
        if (!$cExt instanceof \Gveniver\Extension\Extension) {
            $this->getApplication()->trace->addLine(
                '[%s] Extension ("%s") not found for Smarty query.',
                __CLASS__,
                $sExtensionName
            );

            // Fill variable if specified.
            if ($sVarName)
                $cSmarty->assign($sVarName, null);

            return null;
        }
        
        // Executing query.
        unset($aParams['ext']);
        unset($aParams['act']);
        unset($aParams['var']);
        unset($aParams['format']);
        unset($aParams['cache']);

        // Check cache before executing query.
        if ($mCache) {
            $this->getApplication()->trace->addLine('[%s] Using cache for extension query.', __CLASS__);

            // Try to load cache module from application.
            // On error execute query.
            $cCacheModule = $this->getApplication()->cache;
            if (!$cCacheModule) {
                $this->getApplication()->trace->addLine('[%s] Cache module not found.', __CLASS__);
                $sRet = $cExt->query($sExtensionHandlerName, $aParams, $sFormat);
            } else {
                // Generate identifier of cache data by parameters of call.
                $sCacheId = is_array($mCache) && isset($mCache['id'])
                    ? $mCache['id']
                    : $cCacheModule->generateId(serialize($aCopyParams));
                
                // Load data from cache.
                // If data is not loaded, load by extension and save to cache.
                $sRet = null;
                if ($cCacheModule->get($sCacheId, self::CACHE_GROUP, $sRet)) {
                    $this->getApplication()->trace->addLine('[%s] Data loaded from cache.', __CLASS__);
                } else {
                    $this->getApplication()->trace->addLine('[%s] Data is not loaded from cache.', __CLASS__);
                    $sRet = $cExt->query($sExtensionHandlerName, $aParams, $sFormat);
                    $cCacheModule->set($sRet, $sCacheId, self::CACHE_GROUP);
                }
            }
        } else {
            $sRet = $cExt->query($sExtensionHandlerName, $aParams, $sFormat);
        }

        // Assign result to specified variable.
        if ($sVarName) {
            $cSmarty->assign($sVarName, $sRet);
            return '';
        }
        return $sRet;

    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------