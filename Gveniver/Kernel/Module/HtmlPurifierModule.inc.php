<?php
/**
 * File contains kernel module class for cleaning HTML code.
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
 * Kernel module class for cleaning HTML code.
 * Uses HtmlPurifier library (http://htmlpurifier.org/).
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class HtmlPurifierModule extends BaseModule
{
    /**
     * Cache of configurations.
     *
     * @var array
     */
    private $_aConfigurationCache = array();

    /**
     * Configuration of module.
     *
     * @var array
     */
    private $_aModuleConfiguration = array();

    /**
     * Full initialization of module.
     *
     * @return bool True on success.
     */
    protected function init()
    {
        $this->getApplication()->trace->addLine('[%s] Init.', __CLASS__);

        if (!class_exists('\\HTMLPurifier')) {
            $this->getApplication()->trace->addLine('[%s] Error. HTMLPurifier is not installed.', __CLASS__);
            return false;
        }

        $this->_aModuleConfiguration = $this->getApplication()->getConfig()->get('Module/HtmlPurifierModule');
        if (!is_array($this->_aModuleConfiguration)) {
            $this->_aModuleConfiguration = array();
            $this->getApplication()->trace->addLine('[%s] Configuration of module is not loaded.', __CLASS__);
        } else
            $this->getApplication()->trace->addLine('[%s] Configuration of module is successfully loaded.', __CLASS__);

        $this->getApplication()->trace->addLine('[%s] Init successful.', __CLASS__);
        return true;
    }

    /**
     * Builds configuration object {@see \HTMLPurifier_Config}.
     * Loads additional configuration with specified name.
     *
     * @param string|array|null $mConfiguration The name of additional configuration.
     *
     * @return \HTMLPurifier_Config
     *
     * @throws \Gveniver\Exception\ArgumentException Throws if parameter has wrong type.
     */
    private function _buildConfig($mConfiguration)
    {
        if ($mConfiguration) {
            if (is_string($mConfiguration)) {
                $sConfigurationName = 'name_'.$mConfiguration;
                $aConfiguration = array();
                if ($sConfigurationName && isset($this->_aModuleConfiguration['Configuration'][$mConfiguration]))
                    $aConfiguration = $this->_aModuleConfiguration['Configuration'][$mConfiguration];
            } elseif (is_array($mConfiguration)) {
                $sConfigurationName = 'data_'.md5(serialize($mConfiguration));
                $aConfiguration = $mConfiguration;
            } else
                throw new \Gveniver\Exception\ArgumentException('Parameter $mConfiguration can be array or sting.');
        } else {
            $sConfigurationName = 'empty';
            $aConfiguration = array();
        }

        if (array_key_exists($sConfigurationName, $this->_aConfigurationCache))
            return $this->_aConfigurationCache[$sConfigurationName];

        /** @var $cConfig \HTMLPurifier_Config */
        $cConfig = \HTMLPurifier_Config::createDefault();

        // TODO: caching.
        $cConfig->set('Cache.DefinitionImpl', null);

        $this->_loadArrayConfig('HTML.AllowedElements', $aConfiguration, $cConfig);
        $this->_loadArrayConfig('HTML.AllowedAttributes', $aConfiguration, $cConfig);

        $this->_loadBooleanConfig('AutoFormat.AutoParagraph', $aConfiguration, $cConfig);
        $this->_loadBooleanConfig('AutoFormat.RemoveEmpty.RemoveNbsp', $aConfiguration, $cConfig);
        $this->_loadBooleanConfig('AutoFormat.RemoveEmpty', $aConfiguration, $cConfig);
        $this->_loadBooleanConfig('Core.EscapeInvalidTags', $aConfiguration, $cConfig);

        $this->_loadStringConfig('HTML.DefinitionID', $aConfiguration, $cConfig);
        $this->_loadStringConfig('HTML.Doctype', $aConfiguration, $cConfig);

        return $this->_aConfigurationCache[$sConfigurationName] = $cConfig;
    }

    /**
     * Loads array configuration parameter with merging.
     * Merges data from additional configuration and from base.
     *
     * @param string               $sKey           The name of configuration.
     * @param array                $aConfiguration Additional configuration parameters.
     * @param \HTMLPurifier_Config $cConfig        Configuration object.
     *
     * @return void
     */
    private function _loadArrayConfig($sKey, array $aConfiguration, \HTMLPurifier_Config $cConfig)
    {
        $aData = array();
        if (isset($this->_aModuleConfiguration[$sKey]))
            $aData = explode(',', $this->_aModuleConfiguration[$sKey]);
        if (isset($aConfiguration[$sKey]))
            $aData = array_merge($aData, explode(',', $aConfiguration[$sKey]));
        if (count($aData) > 0)
            $cConfig->set($sKey, $aData);
    }

    /**
     * Loads boolean configuration parameter.
     * Firstly, loads data from additional configuration.
     *
     * @param string               $sKey           The name of configuration.
     * @param array                $aConfiguration Additional configuration parameters.
     * @param \HTMLPurifier_Config $cConfig        Configuration object.
     *
     * @return void
     */
    private function _loadBooleanConfig($sKey, array $aConfiguration, \HTMLPurifier_Config $cConfig)
    {
        if (isset($aConfiguration[$sKey]))
            $cConfig->set($sKey, \Gveniver\toBoolean($aConfiguration[$sKey]));
        elseif (isset($this->_aModuleConfiguration[$sKey]))
            $cConfig->set($sKey, \Gveniver\toBoolean($this->_aModuleConfiguration[$sKey]));
    }

    /**
     * Loads string configuration parameter.
     * Firstly, loads data from additional configuration.
     *
     * @param string               $sKey           The name of configuration.
     * @param array                $aConfiguration Additional configuration parameters.
     * @param \HTMLPurifier_Config $cConfig        Configuration object.
     *
     * @return void
     */
    private function _loadStringConfig($sKey, array $aConfiguration, \HTMLPurifier_Config $cConfig)
    {
        if (isset($aConfiguration[$sKey]))
            $cConfig->set($sKey, $aConfiguration[$sKey]);
        elseif (isset($this->_aModuleConfiguration[$sKey]))
            $cConfig->set($sKey, $this->_aModuleConfiguration[$sKey]);
    }

    /**
     * Cleans specified HTML data.
     *
     * @param string       $sHtml          The data for cleaning.
     * @param string|array $mConfiguration The optional name of HtmlPurifier configuration or array with configuration.
     *
     * @return string Purified HTML.
     */
    public function clean($sHtml, $mConfiguration = null)
    {
        $cPurifier = new \HTMLPurifier($this->_buildConfig($mConfiguration));
        return $cPurifier->purify($sHtml);
    }
}