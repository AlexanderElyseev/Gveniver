<?php
/**
 * File with test case class for session module.
 *
 * @category  Gveniver
 * @package   UnitTest
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

/**
 * Unit test case class for session module.
 *
 * @category  Gveniver
 * @package   UnitTest
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class SessionModuleTest extends PHPUnit_Framework_TestCase
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
            $this->assertNotNull($cApp->session, $sDescription);
        }

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Tests operations of getting and setting values of session module.
     *
     * @return void
     */
    public function testGettingAndSetting()
    {
        foreach ($this->_getApplicationList() as $cApp) {

            /** @var \Gveniver\Kernel\Application $cApp  */

            // Cleaning all.
            $cApp->session->cleanAll();

            // Setting and getting existed value with simple name of attribute.
            $rand1 = rand();
            $sName = 'val'.$rand1;
            $this->assertFalse($cApp->session->contains($sName));
            $cApp->session->set($sName, $rand1);
            $this->assertEquals($rand1, $cApp->session->get($sName));
            $this->assertTrue($cApp->session->contains($sName));

            // Setting and getting existed value with complex name of attribute.
            $sName = 'a/b/c'.$rand1;
            $this->assertFalse($cApp->session->contains($sName));
            $cApp->session->set($sName, $rand1);
            $this->assertEquals($rand1, $cApp->session->get($sName));
            $this->assertTrue($cApp->session->contains($sName));

            $aSubData = $cApp->session->get('a/b');
            $sSubKey = 'c'.$rand1;
            $this->assertInternalType('array', $aSubData);
            $this->assertArrayHasKey($sSubKey, $aSubData);
            $this->assertEquals($rand1, $aSubData[$sSubKey]);

            $aSubData = $cApp->session->get('/a/b');
            $sSubKey = 'c'.$rand1;
            $this->assertInternalType('array', $aSubData);
            $this->assertArrayHasKey($sSubKey, $aSubData);
            $this->assertEquals($rand1, $aSubData[$sSubKey]);

            // Getting all data.
            $aSession = $cApp->session->getAll();
            $this->assertTrue(is_array($aSession));
            $this->assertEquals(2, count($aSession));
            $this->assertTrue(isset($aSession['a']['b']['c'.$rand1]));

            // Getting nonexisted value.
            $rand3 = rand();
            $sName = 'non_existed_val'.$rand3;
            $this->assertFalse($cApp->session->contains($sName));
            $this->assertNull($cApp->session->get($sName));
            $this->assertEquals($rand3, $cApp->session->get($sName.$rand3, $rand3));
        }

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Tests cleaning operations of session module.
     *
     * @return void
     */
    public function testCleaning()
    {
        foreach ($this->_getApplicationList() as $cApp) {

            // Cleaning all.
            $cApp->session->cleanAll();
            $this->assertEquals(0, count($cApp->session->getAll()));

            // Cleaning value.
            $rand = rand();
            $sName = 'val'.$rand;
            $cApp->session->set($sName, $rand);
            $this->assertEquals($rand, $cApp->session->clean($sName));
            $this->assertNull($cApp->session->get($sName));
            $this->assertEquals($rand, $cApp->session->get($sName, $rand));

            $rand = rand();
            $sName = 'a/b/c'.$rand;
            $cApp->session->set($sName, $rand);
            $this->assertEquals($rand, $cApp->session->clean($sName));
            $this->assertNull($cApp->session->get($sName));
            $this->assertEquals($rand, $cApp->session->get($sName, $rand));

            // Cleaning nonexisted value.
            $sName = 'x/y/z'.$rand;
            $this->assertNull($cApp->session->clean($sName));
        }

    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------