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
     * Tests correct initialization of session module.
     * 
     * @return void
     */
    public function testInit()
    {
        $this->assertNotNull(getApplication()->session);

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Tests operations of getting and setting values of session module.
     *
     * @return void
     */
    public function testGettingAndSetting()
    {
        $cApp = getApplication();

        // Setting and getting existed value with simple name of attribute.
        $rand1 = rand();
        $sName = 'val'.$rand1;
        $this->assertFalse($cApp->session->contains($sName));
        $cApp->session->set($sName, $rand1);
        $this->assertEquals($rand1, $cApp->session->get($sName));
        $this->assertEquals($rand1, $cApp->session->get($sName));
        $this->assertTrue($cApp->session->contains($sName));

        // Setting and getting existed value with simple name of attribute.
        $sName = 'a/b/c'.$rand1;
        $this->assertFalse($cApp->session->contains($sName));
        $cApp->session->set($sName, $rand1);
        $this->assertEquals($rand1, $cApp->session->get($sName));
        $this->assertEquals($rand1, $cApp->session->get($sName));
        $this->assertTrue($cApp->session->contains($sName));

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

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Tests cleaning operations of session module.
     *
     * @return void
     */
    public function testCleaning()
    {
        $cApp = getApplication();

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

    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------