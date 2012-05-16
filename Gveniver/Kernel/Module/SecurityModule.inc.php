<?php
/**
 * File with security module class.
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
 * Security module class.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class SecurityModule extends BaseModule
{
    /**
     * Arry with the names of path to stored data in session.
     *
     * @var array
     */
    private $_aSessionAttributeName;
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------

    /**
     * Full initialization of module.
     *
     * @return bool True on success.
     */
    protected function init()
    {
        $this->getApplication()->trace->addLine('[%s] Init.', __CLASS__);

        $this->_aSessionAttributeName = array('Gveniver', __CLASS__, 'CheckPoint');

        $this->getApplication()->trace->addLine('[%s] Init successful.', __CLASS__);
        return true;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Class destructor.
     */
    public function __destruct()
    {
        $this->_unsetOldCheckPoints();

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Add checkpoint to security system.
     * Prevent XSRF.
     *
     * @param string $sName Name of checkpoint.
     * @param int    $nTtl  Time to live for checkpoint (in seconds).
     *
     * @return string Token key of checkpont.
     */
    public function addCheckPoint($sName = null, $nTtl = null)
    {
        // Correct timt to live for token.
        $nTtl = (int)$nTtl;
        if (!$nTtl)
            $nTtl = 1200;

        $nNewTtl = \microtime(true) + $nTtl;
        $sNewToken = \md5(rand());

        $aData = $this->getApplication()->session->get($this->_aSessionAttributeName, array());
        if ($sName) {
            if (isset($aData['Named'][$sName])) {
                $this->getApplication()->log->warning(
                    sprintf('[%s] Overriding token with name "%s".', __CLASS__, $sName)
                );
            }

            $aData['Named'][$sName] = array(
                'Ttl'   => $nNewTtl,
                'Token' => $sNewToken
            );
        } else {
            $aData['Unnamed'][] = array(
                'Ttl'   => $nNewTtl,
                'Token' => $sNewToken
            );
        }

        $this->getApplication()->session->set($this->_aSessionAttributeName, $aData);
        return $sNewToken;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Try to release checkpoint by data from user.
     * Prevent XSRF.
     *
     * @param string $sToken Token, specified by user.
     * @param string $sName  Name of checkpoint.
     *
     * @return bool
     */
    public function releaseCheckPoint($sToken, $sName = null)
    {
        $aData = $this->getApplication()->session->get($this->_aSessionAttributeName, array());
        if ($sName) {
            if (!isset($aData['Named'][$sName]))
                return false;

            $aToken = &$aData['Named'][$sName];
            if ($this->_checkCheckPoint($aToken, $sToken))
                unset($aToken);
        } else
            foreach ($aData['Unnamed'] as $nTokenIndex => $aToken)
                if ($this->_checkCheckPoint($aToken, $sToken))
                    unset($aData['Unnamed'][$nTokenIndex]);

        $this->getApplication()->session->set($this->_aSessionAttributeName, $aData);
        return true;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Compare checkpoint data with data from user.
     *
     * @param array  $aToken Data of caheckpoint.
     * @param string $sToken Token of checkpoint for checking.
     *
     * @return bool
     */
    private function _checkCheckPoint(array $aToken, $sToken)
    {
        // Check TTL.
        if ($aToken['Ttl'] && microtime(true) > $aToken['Ttl'])
            return false;

        // Check tokens.
        if ($aToken['Token'] !== $sToken)
            return false;

        return true;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Unset checkpoints with expired time to live.
     *
     * @return void
     */
    private function _unsetOldCheckPoints()
    {
        if (!$this->getApplication()->session->contains($this->_aSessionAttributeName))
            return;

        $aData = $this->getApplication()->session->get($this->_aSessionAttributeName, array());
        if (isset($aData['Named']))
            foreach ($aData['Named'] as $sName => $aToken)
                if ($aToken['Ttl'] && microtime(true) > $aToken['Ttl'])
                    unset($aData[$sName]);

        if (isset($aData['Unnamed']))
            foreach ($aData['Unnamed'] as $nKey => $aToken)
                if ($aToken['Ttl'] && microtime(true) > $aToken['Ttl'])
                    unset($aData[$nKey]);

        $this->getApplication()->session->set($this->_aSessionAttributeName, $aData);

    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------