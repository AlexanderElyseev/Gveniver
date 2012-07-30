<?php
/**
 * File with test case class for invar module.
 *
 * @category  Gveniver
 * @package   UnitTest
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

/**
 * Unit test case class for invar module.
 *
 * @category  Gveniver
 * @package   UnitTest
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class InvarModuleTest extends PHPUnit_Framework_TestCase
{
    /**
     * Application for testing.
     * Uses dummy session storage.
     *
     * @var Gveniver\Kernel\Application
     */
    private $_cAppWithSimpleInvarLoader;
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------

    /**
     * Initializes class instance.
     * Creates specific applications for tests.
     */
    public function __construct()
    {
        $this->_cAppWithSimpleInvarLoader = getApplication(array('Kernel' => array('StartSession' => false)));

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
            'With simple invar loader' => $this->_cAppWithSimpleInvarLoader
        );

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Sets up the fixtures before running the tests.
     *
     * @return void
     */
    public function setUp()
    {
        $_GET = array(
            'var_get_int' => '1',
            'var_get_str' => 'dfsdfpsdkfpsdokfpsodkfpokspdkfpsodkfp',
            'var'         => 'get'
        );

        $_POST = array(
            'var_post_int' => '2',
            'var_post_str' => 'asdfasdftgrfeasdfdsfgtgrfsdwefrtffrr',
            'var'          => 'post'
        );

        // TODO: Build _REQUEST array.

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Removes fixtures after running the tests.
     *
     * @return void
     */
    public function tearDown()
    {
        $_GET = array();
        $_POST = array();
        $_REQUEST = array();

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Tests correct initialization of invar modules.
     *
     * @return void
     */
    public function testInit()
    {
        foreach ($this->_getApplicationList() as $sDescription => $cApp) {
            $this->assertNotNull($cApp, $sDescription);
            $this->assertNotNull($cApp->invar, $sDescription);
        }

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Tests operations of invar module.
     *
     * @return void
     */
    public function testOperations()
    {
        foreach ($this->_getApplicationList() as $sDescription => $cApp) {
            /** @var $cApp \Gveniver\Kernel\Application */

            $this->setUp();

            // Loading undefined variables.
            $sUndefinedVarKey = 'undefined_var_'.rand();
            $this->assertNull($cApp->invar->get($sUndefinedVarKey), $sDescription);
            $this->assertNull($cApp->invar->post($sUndefinedVarKey), $sDescription);

            $mResult = 'old value get';
            $this->assertFalse($cApp->invar->get($sUndefinedVarKey, null, $mResult), $sDescription);
            $this->assertEquals('old value get', $mResult, $sDescription);

            $mResult = 'old value post';
            $this->assertFalse($cApp->invar->post($sUndefinedVarKey, $mResult), $sDescription);
            $this->assertEquals('old value post', $mResult, $sDescription);

            // Loading defined variables.
            $this->assertEquals('1', $cApp->invar->get('var_get_int'));
            $this->assertEquals('2', $cApp->invar->post('var_post_int'));

            $mResult = 'old value get';
            $this->assertTrue($cApp->invar->get('var_get_str', null, $mResult), $sDescription);
            $this->assertEquals('dfsdfpsdkfpsdokfpsodkfpokspdkfpsodkfp', $mResult, $sDescription);

            $mResult = 'old value post';
            $this->assertTrue($cApp->invar->post('var_post_str', $mResult), $sDescription);
            $this->assertEquals('asdfasdftgrfeasdfdsfgtgrfsdwefrtffrr', $mResult, $sDescription);

            // Loading variables from GET or REQUEST.
            $this->assertEquals('get', $cApp->invar->get('var'));

            // TODO: Build _REQUEST array.
            //$this->assertEquals('get', $cApp->invar->get('var', \Gveniver\Kernel\Module\InvarModule::TARGET_ONLY_GET), $sDescription);
            //$this->assertEquals('get', $cApp->invar->get('var', \Gveniver\Kernel\Module\InvarModule::TARGET_ONLY_REQUEST), $sDescription);
            //$this->assertEquals('get', $cApp->invar->get('var', \Gveniver\Kernel\Module\InvarModule::TARGET_FIRST_GET), $sDescription);
            //$this->assertEquals('get', $cApp->invar->get('var', \Gveniver\Kernel\Module\InvarModule::TARGET_FIRST_REQUEST), $sDescription);

            // Loading variables with filtrations.
            // TODO:

            // Building links.
            // TODO:

            $this->tearDown();
        }

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Tests GvInvarExt extension.
     *
     * @return void
     */
    public function testExtension()
    {
        foreach ($this->_getApplicationList() as $sDescription => $cApp) {
            /** @var $cApp \Gveniver\Kernel\Application */

            $this->setUp();

            // Loading extension.
            $cExt = $cApp->extension->getExtension('GvInvarExt');
            $this->assertNotNull($cExt, $sDescription);
            $this->assertInstanceOf('Gveniver\\Extension\\GvInvarExt', $cExt, $sDescription);

            // Use extension like a simple extension.
            /** @var $cExt \Gveniver\Extension\SimpleExtension */

            // Loading undefined variables.
            $sUndefinedVarKey = 'undefined_var_'.rand();
            $this->assertNull($cExt->query('getVariable', array($sUndefinedVarKey)));
            $this->assertNull($cExt->query('getVariable', array($sUndefinedVarKey, true)));
            $this->assertNull($cExt->query('getVariable', array($sUndefinedVarKey, false)));

            // Loading defined variables.
            $this->assertEquals('1', $cExt->query('getVariable', array('var_get_int')));
            $this->assertEquals('1', $cExt->query('getVariable', array('var_get_int', false)));
            $this->assertNull($cExt->query('getVariable', array('var_get_int', true)));

            $this->assertEquals('2', $cExt->query('getVariable', array('var_post_int', true)));
            $this->assertNull($cExt->query('getVariable', array('var_post_int', false)));
            $this->assertNull($cExt->query('getVariable', array('var_post_int')));

            // Loading variables with filter.
            // TODO:

            // Building links.
            // TODO:

            $this->tearDown();
        }

    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------