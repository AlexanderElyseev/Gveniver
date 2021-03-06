<?php
/**
 * File contains base abstract extension class.
 *
 * @category  Gveniver
 * @package   Extension
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

namespace Gveniver\Extension;

/**
 * Base abstract extension class.
 *
 * @category  Gveniver
 * @package   Extension
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 * @abstract
 */
abstract class BaseExtension
{
    /**
     * Name of cache group for caching extension replies.
     *
     * @var string
     */
    const CACHE_GROUP = 'extension';

    /**
     * Configuration of extension.
     *
     * @var \Gveniver\Config
     */
    private $_cConfig;

    /**
     * Current application of extension.
     *
     * @var \Gveniver\Kernel\Application
     */
    private $_cApplication;

    /**
     * Base extension constructor.
     *
     * @param \Gveniver\Kernel\Application $cApplication Current application.
     */
    public function __construct(\Gveniver\Kernel\Application $cApplication)
    {
        $this->_cApplication = $cApplication;
        $this->_cConfig = new \Gveniver\Config();
    }

    /**
     * Getter for current application.
     *
     * @return \Gveniver\Kernel\Application
     */
    public function getApplication()
    {
        return $this->_cApplication;
    }

    /**
     * Query to extension.
     *
     * @param string $sAction  Name of action handler.
     * @param array  $aParams  Arguments to extension query.
     * @param array  $aOptions Options for query.
     * Elements:
     * ['format']   - target output format.
     * ['external'] - external call of extension (required permessions in export list).
     *
     * @return mixed
     * @abstract
     */
    public abstract function query($sAction, $aParams = array(), $aOptions = array());

    /**
     * Getter for extension configuration.
     * 
     * @return \Gveniver\Config
     */
    public function getConfig()
    {
        return $this->_cConfig;
    }

    /**
     * Load resource of extension by name with spwcified locale.
     *
     * @param string $sResourceName Name of resource for load.
     * @param string $sLocale       Target locale of resource.
     *
     * @return mixed
     */
    public function getResource($sResourceName, $sLocale = null)
    {
        // Load list with all resources.
        $aResourceList = array();
        if (!$this->getConfig()->get('Extension/ResourceList', $aResourceList))
            return null;

        // Load with name of resource and specified locale.
        if ($sResourceName && $sLocale)
            foreach ($aResourceList as $aResource)
                if (isset($aResource['Value'])
                    && isset($aResource['Name']) && $aResource['Name'] == $sResourceName
                    && isset($aResource['Locale']) && $aResource['Locale'] == $sLocale
                )
                    return $aResource['Value'];

        // Load only with name of resource.
        if ($sResourceName)
            foreach ($aResourceList as $aResource)
                if (isset($aResource['Value']) && isset($aResource['Name']) && $aResource['Name'] == $sResourceName)
                    return $aResource['Value'];

        return null;
    }
}