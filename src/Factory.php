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

namespace Apix\SimpleCache;

use Apix\Cache;

/**
 * Factory class that provides a PSR-16 (Simple Cache) wrapper to Apix-Cache.
 *
 * @author Franck Cassedanne <franck at ouarz.net>
 */
class Factory extends Cache\Factory
{

    /**
     * {@inheritdoc}
     */
    public static function getPool(
        $mix, array $options=array(), $taggable=false
    ) {
        $pool = parent::getPool($mix, $options, $taggable);

        return $taggable
                ? new PsrSimpleCache\TaggablePool($pool)
                : new PsrSimpleCache\Pool($pool);
    }

    /**
     * @see self::getPool
     * @return TaggablePool
     */
    public static function getTaggablePool($mix, array $options=array())
    {
        return self::getPool($mix, $options, true);
    }
}
