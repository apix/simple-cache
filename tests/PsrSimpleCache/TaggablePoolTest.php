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
use Apix\Cache\PsrCache\TaggablePool as PsrCache;
use Apix\SimpleCache\PsrSimpleCache\TaggablePool;

class TaggablePoolTest extends \PHPUnit_Framework_TestCase
{
    protected $pool = null;
    protected $tags = array('fooTag', 'barTag');

    public function setUp()
    {
        $this->pool = new TaggablePool(
            new PsrCache(new Cache\Runtime())
        );
        $this->pool->setTags($this->tags);
    }

    public function tearDown()
    {
        unset($this->pool);
    }

    public function testGetTags()
    {
        $this->assertSame($this->pool->getTags(), $this->tags);

        $this->assertSame($this->pool, $this->pool->setTags(null));
        $this->assertNull($this->pool->getTags());
    }

    public function testGetMultipleByTagIsEmptyArrayByDefault()
    {
        $this->assertEquals(
            array(),
            $this->pool->getMultipleByTag('non-existant')
        );
    }

    public function testSetAndGetMultipleByTag()
    {
        $values =array('foo1' => 'foo1Value', 'foo2' => 'foo2Value');

        $this->assertTrue($this->pool->setMultiple($values));

        $this->assertCount(2, $this->pool->getMultipleByTag('fooTag'));
        $this->assertCount(0, $this->pool->getMultipleByTag('nonTag'));
    }

    public function testClearByTags()
    {
        $this->assertTrue($this->pool->set('foo', 'fooValue'));

        $this->assertTrue($this->pool->clearByTags(array('fooTag')));
        $this->assertFalse($this->pool->has('foo'));
    }

    public function testClearByTagsWithNonExistantReturnFalse()
    {
        $this->assertFalse($this->pool->clearByTags(array('non-existant')));
    }
}
