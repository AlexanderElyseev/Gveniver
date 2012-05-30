<?php
/**
 * File with test case class for cache module.
 *
 * @category  Gveniver
 * @package   UnitTest
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

/**
 * Unit test case class for cache module.
 *
 * @category  Gveniver
 * @package   UnitTest
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class CacheModuleTest extends PHPUnit_Framework_TestCase
{
    /**
     * Application with memcached as default cache provider.
     *
     * @var Gveniver\Kernel\Application
     */
    private $_cAppWithMemcache;
    //-----------------------------------------------------------------------------

    /**
     * Application with file-memory cache provider.
     *
     * @var Gveniver\Kernel\Application
     */
    private $_cAppWithFileMemoryCache;
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------

    /**
     * Initializes class instance.
     * Creates specific applications for tests.
     */
    public function __construct()
    {
        $this->_cAppWithMemcache = getApplication(
            array(
                'Kernel' => array('StartSession' => false),
                'Module' => array(
                    'CacheModule' => array(
                        'Providers' => array(
                            array(
                                'ProviderClass' => 'MemcachedCacheProvider',
                                'ProviderName' => 'Memcached',
                                'Args' => array(
                                    'Servers' => array(
                                        array(
                                            'Name' => 'localhost',
                                            'Host' => '127.0.0.1'
                                        )
                                    )
                                )
                            )
                        )
                    )
                )
            )
        );

        $this->_cAppWithFileMemoryCache = getApplication(
            array(
                'Kernel' => array('StartSession' => false),
                'Module' => array(
                    'CacheModule' => array(
                        'Providers' => array(
                            array(
                                'ProviderClass' => 'FileMemoryCacheProvider',
                                'ProviderName' => 'FileMemory',
                                'Args' => array(
                                    'FileMode' => '0777',
                                    'DirMode'  => '0777'
                                )
                            )
                        )
                    )
                )
            )
        );

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
            'With memcached'   => $this->_cAppWithMemcache,
            'With file-memory' => $this->_cAppWithFileMemoryCache
        );

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Tests correct initialization of cache module.
     *
     * @return void
     */
    public function testInit()
    {
        foreach ($this->_getApplicationList() as $sDescription => $cApp) {
            $this->assertNotNull($cApp, $sDescription);
            $this->assertNotNull($cApp->cache, $sDescription);
            $this->assertNotNull($cApp->cache->getProvider(), $sDescription);
        }

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Tests operations of cache module.
     *
     * @return void
     */
    public function testOperations()
    {
        foreach ($this->_getApplicationList() as $sDescription => $cApp) {

            /** @var $cApp Gveniver\Kernel\Application */

            // Checking cleaning all cached data.
            $this->assertTrue($cApp->cache->cleanAll(), $sDescription);

            // Checking loading of data with undefined keys.
            $this->assertNull($cApp->cache->get('undefined_key'));

            // Checking saving of data.
            $this->assertTrue($cApp->cache->set('value1', 'defined_key1', array('tag1', 'tag2')), $sDescription);
            $this->assertTrue($cApp->cache->set('value2', 'defined_key2', 'tag1'), $sDescription);
            $this->assertTrue($cApp->cache->set('value3', 'defined_key3', 'tag2'), $sDescription);
            $this->assertTrue($cApp->cache->set('value4', 'defined_key4', array('tag3'), 1), $sDescription);

            // Checking loading of data.
            $this->assertEquals('value1', $cApp->cache->get('defined_key1'), $sDescription);
            $this->assertEquals('value2', $cApp->cache->get('defined_key2'), $sDescription);
            $this->assertEquals('value3', $cApp->cache->get('defined_key3'), $sDescription);
            $this->assertNotEquals('value2', $cApp->cache->get('defined_key1'), $sDescription);

            // Checking cleaning of data.
            $this->assertTrue($cApp->cache->clean('defined_key1'), $sDescription);
            $this->assertNull($cApp->cache->get('defined_key1'), $sDescription);

            $this->assertTrue($cApp->cache->cleanByTags(array('tag1')), $sDescription);
            $this->assertNull($cApp->cache->get('defined_key2'), $sDescription);
            $this->assertNotNull($cApp->cache->get('defined_key3'), $sDescription);

            $this->assertTrue($cApp->cache->cleanByTags('tag2'), $sDescription);
            $this->assertNull($cApp->cache->get('defined_key2'), $sDescription);
            $this->assertNull($cApp->cache->get('defined_key3'), $sDescription);

            // Checking TTL.
            usleep(2000000);
            $this->assertNull($cApp->cache->get('defined_key4'), $sDescription);

            // Cleaning all after tests.
            $cApp->cache->cleanAll();

        } // End foreach

    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------