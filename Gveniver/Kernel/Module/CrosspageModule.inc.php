<?php
/**
 * File contains class of module for redirections.
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
 * Class of module for redirections.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class CrosspageModule extends BaseModule
{
    /**
     * Full initialization of module.
     *
     * @return bool True on success.
     */
    protected function init()
    {
        $this->getApplication()->trace->addLine('[%s] Init.', __CLASS__);

        // Cleans all data in 1/10 cases.
        if (rand(0, 10) == 0)
            $this->_cleanAll();

        $this->getApplication()->trace->addLine('[%s] Init successful.', __CLASS__);
        return true;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Generates an unique random key for cross-page data set.
     *
     * @return string
     */
    public function generateDataKey()
    {
        return md5(uniqid(rand(), true));

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Checks all cross-page data and cleans expired and incorrect.
     *
     * @return void
     */
    private function _cleanAll()
    {
        $aData = $this->getApplication()->session->get(
            $this->_getSessionKey()
        );

        if (!is_array($aData))
            return;

        foreach ($aData as $sKey => $aItem)
            if (!$this->_check($aData))
                $this->cleanData($sKey);

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Checks specified data.
     *
     * @param array $aData Data for checking.
     *
     * @return boolean Returns true if data is correct.
     */
    private function _check($aData)
    {
        return $aData && is_array($aData) && isset($aData['data']) && isset($aData['time']) && (time() - $aData['time'] < 86400);

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Gets the cross-page data with specified key.
     *
     * @param string $sKey     The key of cross-page data set.
     * @param mixed  $mDefault The default data.
     *
     * @return mixed
     */
    public function getData($sKey, $mDefault = null)
    {
        $aData = $this->getApplication()->session->get(
            $this->_getSessionKey($sKey)
        );

        if (!$this->_check($aData)) {
            $this->cleanData($sKey);
            return $mDefault;
        }

        return $aData['data'];

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
        $this->getApplication()->session->set(
            $this->_getSessionKey($sKey),
            array(
                'data' => $mData,
                'time' => time()
            )
        );

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Cleans the cross-page data set with specified key.
     *
     * @param string $sKey The key of cross-page data set.
     *
     * @return mixed Cleaned value.
     */
    public function cleanData($sKey)
    {
        return $this->getApplication()->session->clean(
            $this->_getSessionKey($sKey)
        );

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Generates a path in session for saving cross-page data set with specified key.
     * If the key is not defined, generates a path to all cross-page data of user.
     *
     * @param string $sKey The key of cross-page data set.
     *
     * @return array
     */
    private function _getSessionKey($sKey = null)
    {
        return $sKey ? array('_gv-cross-page', $sKey) : array('_gv-cross-page');

    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------