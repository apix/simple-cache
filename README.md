Apix-SimpleCache, the PSR-16 extension to [Apix-Cache](//github.com/apix/cache)
=================================
[![Latest Stable Version](https://poser.pugx.org/apix/simple-cache/v/stable.svg)](https://packagist.org/packages/apix/simple-cache)  [![Build Status](https://scrutinizer-ci.com/g/apix/simple-cache/badges/build.png?b=master)](https://scrutinizer-ci.com/g/apix/simple-cache/build-status/master)  [![Code Quality](https://scrutinizer-ci.com/g/apix/simple-cache/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/apix/simple-cache/?branch=master) [![Code Coverage](https://scrutinizer-ci.com/g/apix/simple-cache/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/apix/simple-cache/?branch=master) [![License](https://poser.pugx.org/apix/simple-cache/license.svg)](https://packagist.org/packages/apix/simple-cache) [![Build Status](https://travis-ci.org/apix/simple-cache.png?branch=master)](https://travis-ci.org/apix/simple-cache)

Apix-SimpleCache provides PSR-16 to **[Apix-Cache](//github.com/apix/cache)** permitting easy caching and invalidation...

* Fully **unit-tested** and compliant with PSR-1, PSR-2, PSR-4 and PSR-16.
* Continuously integrated with **PHP** **5.3**, **5.4**, **5.5**, **5.6**, **7.*** and **HHVM**.

⇄ *[Pull requests](//github.com/apix/simple-cache/blob/master/.github/CONTRIBUTING.md)* and ★ *Stars* are always welcome. For bugs and feature request, please [create an issue](//github.com/apix/simple-cache/issues/new).

---

Basic usage
-----------

```php
  use Apix\SimpleCache;

  $client = new \Redis();
  #$client = new \PDO('sqlite:...');    // Any supported client object e.g. Memcached, MongoClient, ...
  #$client = new Cache\Files($options); // or one that implements Apix\Cache\Adapter
  #$client = 'apc';                     // or an adapter name (string) e.g. "APC", "Runtime"
  #$client = new MyArrayObject();       // or even a plain array() or \ArrayObject.

  $cache = SimpleCache\Factory::getPool($client);           // without tagging support
  #$cache = SimpleCache\Factory::getTaggablePool($client);  // with tagging
    
  if ( !$cache->has('wibble_id') ) {
    $data = compute_slow_and_expensive_stuff();
    $cache->set('wibble_id', $data);
  }

  return $cache->get('wibble_id');
```

Installation
------------------------

This project adheres to [Semantic Versioning](http://semver.org/) and can be installed using composer:  

    $ composer require apix/simple-cache:1.0.*

All notable changes to this project are documented in its [CHANGELOG](CHANGELOG.md).

License
-------
This work is licensed under the New BSD license -- see the [LICENSE](LICENSE.txt) for the full details.<br>Copyright (c) 2010-2017 Franck Cassedanne
