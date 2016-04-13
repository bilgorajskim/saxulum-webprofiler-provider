saxulum webprofiler provider
===========================

This is a Silex 2 compatible version of [saxulum/saxulum-webprofiler-provider][1]

**It does not support Doctrine ODM**

Features
--------

* Enhance the default silex web profiler with database informations

Requirements
------------

* php >=5.3
* jdorn/sql-formatter ~1.1
* psr/log 1.0.*
* silex/silex ~2.0
* silex/web-profiler ~2.0
* symfony/doctrine-bridge ~2.3


Installation
------------

The [SilexWebProfiler][1] from silex itself is needed!

```php
$app->register(new Silex\Provider\WebProfilerServiceProvider(), array(
    'profiler.cache_dir' => __DIR__.'/../cache/profiler',
    'profiler.mount_prefix' => '/_profiler', // this is the default
));
```

```php
$app->register(new Saxulum\SaxulumWebProfiler\Provider\SaxulumWebProfilerProvider());
```

[1]: https://github.com/silexphp/Silex-WebProfiler
[2]: https://github.com/saxulum/saxulum-webprofiler-provider
