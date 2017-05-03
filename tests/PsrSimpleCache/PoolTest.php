<?php

/**
 *
 * This file is part of the Apix Project.
 *
 * (c) Franck Cassedanne <franck at ouarz.net>
 *
 * @license     http://opensource.org/licenses/BSD-3-Clause  New BSD License
 *
 */

namespace Apix\SimpleCache\tests\PsrSimpleCache;

use Apix\Cache;
use Apix\Cache\PsrCache\Pool as PsrCache;
use Apix\SimpleCache\PsrSimpleCache\Pool as PsrSimpleCache;

class PoolTest extends \PHPUnit_Framework_TestCase
{
    protected $pool = null;

    protected $items = array('key1' => 'value1', 'key2' => 'value2');

    public function setUp()
    {
        $this->pool = new PsrSimpleCache(
            new PsrCache(new Cache\Runtime())
        );
    }

    public function tearDown()
    {
        unset($this->pool);
    }

    public function testBasicSetAndGetOperations()
    {
        $this->assertTrue($this->pool->set('key', 'value'));
        $this->assertEquals('value', $this->pool->get('key'));
    }

    public function testGetItemWithNonExistantKeyReturnNull()
    {
        $this->assertNull($this->pool->get('non-existant'));
    }

    public function testGetItemWithNonExistantKeyReturnTheProvidedDefault()
    {
        $this->assertSame(
            'default-value',
            $this->pool->get('non-existant', 'default-value')
        );
    }

    /**
     * @dataProvider _invalidSingleKeyProvider
     * @expectedException \Psr\SimpleCache\InvalidArgumentException
     */
    public function testGetWillThrowsException($key)
    {
        $this->pool->get($key);
    }

    /**
     * @dataProvider _invalidSingleKeyProvider
     * @expectedException \Psr\SimpleCache\InvalidArgumentException
     */
    public function testSetWillThrowsException($key)
    {
        $this->pool->set($key, 'value');
    }

    public function testSetExpired()
    {
        $this->assertTrue(
            $this->pool->set('key1', 'value1', -10)
        );
        $this->assertFalse($this->pool->has('key1'));
    }

    public function testDelete()
    {
        $this->assertTrue($this->pool->set('key', 'value'));
        $this->assertTrue($this->pool->delete('key'));
        $this->assertFalse($this->pool->has('key'));
    }

    /**
     * It MUST NOT be considered an error condition if the specified key does
     * not exist. The post-condition is the same (the key does not exist, or
     * the pool is empty), thus there is no error condition.
     */
    public function testDeleteWithNonExistantKey()
    {
        $this->assertTrue($this->pool->delete('non-existant'));
        $this->assertFalse($this->pool->has('non-existant'));
    }

    /**
     * @dataProvider _invalidSingleKeyProvider
     * @expectedException \Psr\SimpleCache\InvalidArgumentException
     */
    public function testDeleteWithAnInvalidKey($key)
    {
        $this->pool->delete($key);
    }

    public function testClear()
    {
        $this->assertTrue($this->pool->set('key', 'value'));
        $this->assertTrue($this->pool->clear());
        $this->assertFalse($this->pool->has('key'));
    }

    public function testSetMultipleAndGetMultiple()
    {
        $this->assertTrue($this->pool->setMultiple($this->items));
        $this->assertSame(
            $this->items,
            $this->pool->getMultiple(array('key1', 'key2'))
        );
    }

    public function testSetMultipleAndGetMultipleWithTraversable()
    {
        $this->assertTrue($this->pool->setMultiple(
            new \ArrayIterator($this->items))
        );
        $this->assertSame(
            $this->items,
            $this->pool->getMultiple(new \ArrayIterator(array('key1', 'key2')))
        );
    }

    public function testGetMultipleWithNonExistantKey()
    {
        $this->assertTrue($this->pool->setMultiple($this->items));
        $this->assertSame(
            $this->items + array('non-existant' => null),
            $this->pool->getMultiple(['key1', 'key2', 'non-existant'])
        );
    }

    public function testGetMultipleWithNonExistantKeyReturnTheProvidedDefault()
    {
        $this->assertTrue($this->pool->set('key1', 'value1'));
        $this->assertSame(
            array('key1' => 'value1', 'key2' => 'default-value'),
            $this->pool->getMultiple(array('key1', 'key2'), 'default-value')
        );
    }

    /**
     * @dataProvider _invalidMultiKeyProvider
     * @expectedException \Psr\SimpleCache\InvalidArgumentException
     */
    public function testGetMultipleWithInvalidKeys($keys)
    {
        $this->pool->getMultiple($keys);
    }

    /**
     * @dataProvider _invalidMultiKeyValueProvider
     * @expectedException \Psr\SimpleCache\InvalidArgumentException
     */
    public function testSetMultipleWithInvalidKeys($keys)
    {
        $this->pool->setMultiple($keys);
    }

    public function testSetMultipleWithExpired()
    {
        $this->assertFalse(
            $this->pool->setMultiple(array('key' => 'value'), -1)
        );

        $this->assertFalse($this->pool->has('key'));
    }

    /**
     * @expectedException \Psr\SimpleCache\InvalidArgumentException
     */
    public function testSetMultipleWithWongTtl()
    {
        $this->assertFalse(
            $this->pool->setMultiple(array('key' => 'value'), 'bad-ttl')
        );

        $this->assertFalse($this->pool->has('key'));
    }


    public function testDeleteMultiple()
    {
        $this->assertTrue($this->pool->setMultiple($this->items));

        $this->assertTrue(
            $this->pool->deleteMultiple(array('key1', 'key2'))
        );
        $this->assertFalse($this->pool->has('key1'));
        $this->assertFalse($this->pool->has('key2'));
    }

    public function testDeleteMultipleWithTraversable()
    {
        $this->assertTrue($this->pool->setMultiple($this->items));

        $this->assertTrue( $this->pool->deleteMultiple(
            new \ArrayIterator(array('key1', 'key2'))
        ) );

        $this->assertFalse($this->pool->has('key1'));
        $this->assertFalse($this->pool->has('key2'));
    }

    /**
     * @dataProvider _invalidMultiKeyProvider
     * @expectedException \Psr\SimpleCache\InvalidArgumentException
     */
    public function testDeleteMultipleInvalidKeys($keys)
    {
        $this->pool->deleteMultiple($keys);
    }

    public function testHas()
    {
        $this->assertFalse($this->pool->has('foo'));
        $this->pool->set('foo', 'bar');
        $this->assertTrue($this->pool->has('foo'));
    }

    /**
     * @dataProvider _invalidSingleKeyProvider
     * @expectedException \Psr\SimpleCache\InvalidArgumentException
     */
    public function testHasWithInvalidKey($key)
    {
        $this->pool->has($key);
    }

    public function testGetCacheAdapter()
    {
        $this->assertInstanceOf(
            '\Apix\Cache\Runtime',
            $this->pool->getCacheAdapter()
        );
    }

    /**
     * The following characters are reserved for future extensions
     * and MUST NOT be supported by implementing libraries:
     *      {}()/\@:
     *
     * @return array
     */
    protected static function _invalidKeyProvider()
    {
        return array(
            array('foo{bar'),
            array('foo}bar'),
            array('foo(bar'),
            array('foo)bar'),
            array('foo/bar'),
            array('foo\\bar'),
            array('foo@bar'),
            array('foo:bar'),
            'null'    => array( null ),
            'boolean' => array( true ),
            'integer' => array( 1 ),
            'float'   => array( 1.1 )
        );
    }

    /**
     * Addition to invalidKeyProvider for `get`, `set`, `delete` & `has` (all
     * the methods that take a single key).
     *
     * @return array
     */
    public static function _invalidSingleKeyProvider()
    {
        $keys = static::_invalidKeyProvider();

        return array_merge($keys, [
            'array'  => array( array() ),
            'object' => array( new \stdClass() ),
        ]);
    }

    /**
     * Addition to invalidKeyProvider for `getMultiple` & `deleteMultiple` (all
     * the methods that take an array/Traversable of keys).
     *
     * @return array
     */
    public static function _invalidMultiKeyProvider()
    {
        $keys = static::_invalidKeyProvider();

        $return = array_merge($keys, array(
            'object' => array( new \stdClass() ),
        ));

        foreach ($keys as $input) {
            $key = array($input[0]);
            $return[] = array( $key );
            $return[] = array( new \ArrayIterator($key));
        }

        return $return;
    }

    /**
     * Addition to invalidKeyProvider for `setMultiple` (all the methods that
     * take an array/Traversable of keys => values).
     *
     * @return array
     */
    public static function _invalidMultiKeyValueProvider()
    {
        $keys = static::_invalidKeyProvider();

        $return = array_merge($keys, array(
            'object' => array( new \stdClass() ),
        ));

        foreach ($keys as $input) {
            $key = array( $input[0] => 'value' );
            $return[] = array( $key );
            $return[] = array( new \ArrayIterator($key));
        }

        return $return;
    }
}
