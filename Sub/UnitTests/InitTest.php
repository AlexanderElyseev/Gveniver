<?php
/**
 * File with base unit test cases.
 *
 * @category  Gveniver
 * @package   UnitTest
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

/**
 * Unit test case class for testing initialization of kernel.
 *
 * @category  Gveniver
 * @package   UnitTest
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class InitTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test correct initialization of kernel with dummy profile.
     * 
     * @return void
     */
    public function testInit()
    {
        $this->assertNotNull(getApplication());

    } // End function
    //-----------------------------------------------------------------------------
    
    /**
     * Test initialization of kernel with not existed profile.
     *
     * @expectedException Gveniver\Exception\ArgumentException
     * @return void
     */
    public function testWrongProfile()
    {
        new Gveniver\Kernel\Application('WrongProfileNameHere');

    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------