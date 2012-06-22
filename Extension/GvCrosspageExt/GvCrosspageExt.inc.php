<?php
/**
 * File contains extension for working with cross-data kernel module.
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
 * Extension class for working with cross-data kernel module ({@see \Gveniver\Kernel\Module\CrossPageModule}).
 *
 * @category  Gveniver
 * @package   Extension
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class GvCrosspageExt extends SimpleExtension
{
    /**
     * Generates a key for cross-page data set.
     *
     * @return string
     */
    public function generateDataKey()
    {
        return $this->getApplication()->crosspage->generateDataKey();

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Returns the key of cross-page data set from invars.
     * If key is not loaded or incorrect, generates new key.
     *
     * @param string $sInvarName The name of invar.
     *
     * @return string
     */
    public function getDataKeyFromInvarOrGenerate($sInvarName = 'crosspage')
    {
        $aFilter = array('filter' => FILTER_VALIDATE_REGEXP, 'options' => array('options' => array('regexp' => '/^[0-9a-f]{32}$/i')));
        if (!$this->getApplication()->invar->getEx($sInvarName, null, $aFilter, $sKey))
            return $this->generateDataKey();

        return $sKey;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Gets the saved cross-page data with specified key.
     * Returns default if data with specified key is not found.
     *
     * @param string $sKey     The key of cross-page data set.
     * @param mixed  $mDefault The default value that returns if data is not found.
     *
     * @return mixed The cross-page data or default value.
     */
    public function getData($sKey, $mDefault = null)
    {
        return $this->getApplication()->crosspage->getData($sKey, $mDefault);

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Gets the saved cross-page data with specified key.
     * Returns default if data with specified key is not found.
     *
     * @param string $sKey     The key of cross-page data set.
     * @param string $sName    The key in data array.
     * @param mixed  $mDefault The default value that returns if data is not found.
     *
     * @return mixed The cross-page data or default value.
     */
    public function getDataByVarName($sKey, $sName, $mDefault = null)
    {
        $aData = $this->getApplication()->crosspage->getData($sKey);
        if (is_array($aData))
            return isset($aData[$sName]) ? $aData[$sName] : $mDefault;

        return $mDefault;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Sets the cross-page data with specified key.
     *
     * @param string $sKey  The key of cross-page data set.
     * @param mixed  $mData The data for saving.
     *
     * @return void
     */
    public function setData($sKey, $mData)
    {
        $this->getApplication()->crosspage->setData($sKey, $mData);

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Cleans the cross-page data with specified key.
     *
     * @param string $sKey The key of cross-page data set.
     *
     * @return mixed Cleaned value.
     */
    public function cleanData($sKey)
    {
        return $this->getApplication()->crosspage->cleanData($sKey);

    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------