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

    /**
     * Initializes class instance.
     * Creates specific applications for tests.
     */
    public function __construct()
    {
        $this->_cApp = getApplication(array('Kernel' => array('StartSession' => false)));
    }

    /**
     * Tests correct initialization of HtmlPurifier module.
     *
     * @return void
     */
    public function testInit()
    {
        $this->assertNotNull($this->_cApp);
        $this->assertNotNull($this->_cApp->htmlPurifier);
    }

    /**
     * Tests cleaning HTML with configuration from Dummy profile.
     *
     * @return void
     */
    public function testCleaningWithProfileConfig()
    {
        $sHtml = '<a href="#">Hi!</a><script type="text/javascript">alert(document)</script><p>Ololo';
        $sHtmlWithoutScript = '<a href="#">Hi!</a><p>Ololo</p>';
        $this->assertEquals($sHtmlWithoutScript, $this->_cApp->htmlPurifier->clean($sHtml, 'Document'));
    }

    /**
     * Tests output text.
     *
     * @return void
     */
    public function testOutputText()
    {
        $sHtml = '<a href="#">Hi!</a><script type="text/javascript">alert(document)</script><p>Ololo';
        $this->assertEquals('Hi!...', $this->_cApp->htmlPurifier->outputText($sHtml, 3));
    }
}