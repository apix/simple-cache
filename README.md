Apix-Simple-Cache, an extension for Apix-Cache providing a full PSR-16 [![Build Status](https://travis-ci.org/apix/simple-cache.png?branch=master)](https://travis-ci.org/apix/simple-cache)
=================================
[![Latest Stable Version](https://poser.pugx.org/apix/simple-cache/v/stable.svg)](https://packagist.org/packages/apix/simple-cache)  [![Build Status](https://scrutinizer-ci.com/g/apix/simple-cache/badges/build.png?b=master)](https://scrutinizer-ci.com/g/apix/simple-cache/build-status/master)  [![Code Quality](https://scrutinizer-ci.com/g/apix/simple-cache/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/apix/simple-cache/?branch=master)  [![Code Coverage](https://scrutinizer-ci.com/g/apix/simple-cache/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/apix/simple-cache/?branch=master)  [![License](https://poser.pugx.org/apix/simple-cache/license.svg)](https://packagist.org/packages/apix/simple-cache)

APIx Simple-Cache is an extension for Apix-Cache (a PSR-6 compliant cache with tagging facility with many cache backends) which adds  

* **PSR-16** (SimpleCache) standard is provided thru a factory wrapper class.
* Fully unit **tested** and compliant with PSR-1, PSR-2, PSR-4 and PSR-SimpleCache.
* Continuously integrated
  * with **PHP** ~~5.3~~, **5.4**, **5.5**, **5.6**, **7.0** and **HHVM**.

⇄ *[Pull requests](//github.com/apix/simple-cache/blob/master/.github/CONTRIBUTING.md)* and ★ *Stars* are always welcome. For bugs and feature request, please [create an issue](//github.com/apix/simple-cache/issues/new).

---

Basic usage
-----------

```php
  use Apix\SimpleCache;

  $backend = new \Redis();
  #$backend = new \PDO('sqlite:...');    // Any supported client object e.g. Memcached, MongoClient, ...
  #$backend = new Cache\Files($options); // or one that implements Apix\Cache\Adapter
  #$backend = 'apc';                     // or an adapter name (string) e.g. "APC", "Runtime"
  #$backend = new MyArrayObject();       // or even a plain array() or \ArrayObject.

  $pool = SimpleCache\Factory::getPool($backend);   // without tagging support
  #$pool = SimpleCache\Factory::getTaggablePool($backend);    // with tagging
  
  $item = $pool->get('wibble_id');
  
  if ( !$item->isHit() ) {
    $data = compute_expensive_stuff();
    $item->set($data);
    $pool->save($item);
  }

  return $item->get();
```


Advanced usage
--------------
See ... 

Installation
------------------------

This project adheres to [Semantic Versioning](http://semver.org/) and can be installed using composer:  

    $ composer require apix/cache:1.2.*

All notable changes to this project are documented in its [CHANGELOG](CHANGELOG.md).

License
-------
This work is licensed under the New BSD license -- see the [LICENSE](LICENSE.txt) for the full details.<br>Copyright (c) 2010-2017 Franck Cassedanne
