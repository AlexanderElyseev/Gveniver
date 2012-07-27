<?php
/**
 * File with test case class for html-purifier module.
 *
 * @category  Gveniver
 * @package   UnitTest
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2012 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

require_once 'HTMLPurifier.auto.php';

/**
 * Unit test case class for html-purifier module.
 *
 * @category  Gveniver
 * @package   UnitTest
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2012 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class HtmlPurifierModuleTest extends PHPUnit_Framework_TestCase
{
    /**
     * Application for testing.
     *
     * @var Gveniver\Kernel\Application
     */
    private $_cApp;
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------

    /**
     * Initializes class instance.
     * Creates specific applications for tests.
     */
    public function __construct()
    {
        $this->_cApp = getApplication(array('Kernel' => array('StartSession' => false)));

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Tests correct initialization of HtmlPurifier module.
     *
     * @return void
     */
    public function testInit()
    {
        $this->assertNotNull($this->_cApp);
        $this->assertNotNull($this->_cApp->htmlPurifier);

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Tests cleaning HTML.
     *
     * @return void
     */
    public function testCleaning()
    {


    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------