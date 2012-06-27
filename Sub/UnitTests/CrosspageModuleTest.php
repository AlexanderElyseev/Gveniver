<?php
/**
 * File with test case class for cross-page module.
 *
 * @category  Gveniver
 * @package   UnitTest
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

/**
 * Unit test case class for cross-page module.
 *
 * @category  Gveniver
 * @package   UnitTest
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class CrossPageModuleTest extends PHPUnit_Framework_TestCase
{
    /**
     * Application for testing.
     * Uses dummy session storage.
     *
     * @var Gveniver\Kernel\Application
     */
    private $_cAppWithDummySession;
    //-----------------------------------------------------------------------------

    /**
     * Application for testing.
     * Uses native session storage.
     *
     * @var Gveniver\Kernel\Application
     */
    private $_cAppWithNativeSession;
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------

    /**
     * Initializes class instance.
     * Creates specific applications for tests.
     */
    public function __construct()
    {
        $this->_cAppWithDummySession = getApplication(array('Kernel' => array('StartSession' => true), 'Module' => array('SessionModule' => array('StorageClass' => 'DummySessionStorage'))));
        $this->_cAppWithNativeSession = getApplication(array('Kernel' => array('StartSession' => true), 'Module' => array('SessionModule' => array('StorageClass' => 'NativeSessionStorage'))));

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Returns list of applications for testing.
     *
     * @return array
     */
    private function _getApplicationList()
    {
        return array(
            'Dummy session storage' => $this->_cAppWithDummySession,
            'Native session storage' => $this->_cAppWithNativeSession
        );
    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Tests correct initialization of session module.
     *
     * @return void
     */
    public function testInit()
    {
        foreach ($this->_getApplicationList() as $sDescription => $cApp) {
            $this->assertNotNull($cApp, $sDescription);
            $this->assertNotNull($cApp->crosspage, $sDescription);
        }

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Tests operations of crosspage module.
     *
     * @return void
     */
    public function testOperations()
    {
        foreach ($this->_getApplicationList() as $sDescription => $cApp) {

            /** @var \Gveniver\Kernel\Application $cApp  */

            $sKey = $cApp->crosspage->generateDataKey();
            $this->assertInternalType('string', $sKey, $sDescription);

            $cApp->crosspage->setData($sKey, 42);
            $this->assertEquals(42, $cApp->crosspage->getData($sKey), $sDescription);
            $this->assertEquals(42, $cApp->crosspage->getData($sKey.rand(), 42), $sDescription);
            $this->assertNull($cApp->crosspage->getData($sKey.rand()), $sDescription);

            $cApp->crosspage->cleanData($sKey);
            $this->assertNull($cApp->crosspage->getData($sKey), $sDescription);
        }

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Tests operations in crosspage extension.
     *
     * @return void
     */
    public function testExtension()
    {

    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------