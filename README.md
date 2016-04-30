saxulum-doctrine-mongodb-provider
=================================

**works with plain silex-php**

[![Build Status](https://api.travis-ci.org/saxulum/saxulum-doctrine-mongodb-provider.png?branch=master)](https://travis-ci.org/saxulum/saxulum-doctrine-mongodb-provider)
[![Total Downloads](https://poser.pugx.org/saxulum/saxulum-doctrine-mongodb-provider/downloads.png)](https://packagist.org/packages/saxulum/saxulum-doctrine-mongodb-provider)
[![Latest Stable Version](https://poser.pugx.org/saxulum/saxulum-doctrine-mongodb-provider/v/stable.png)](https://packagist.org/packages/saxulum/saxulum-doctrine-mongodb-provider)


Features
--------

 * Support for mongodb within [Silex][1], it does NOT PROVIDE the [ODM][2] integration
 * Support for PHP7 using "alcaeus/mongo-php-adapter" as wrapper for missing ext-mongo module


Requirements
------------

 * PHP 7.0+
 * Doctrine Mongodb 1.0.3+


Notes
-----
 * this version wont be compatible for php <= 5.6.n now
 * this version wont be runnable under cilex and silex < 2.n


Installation
------------

Through [Composer](http://getcomposer.org) as [saxulum/saxulum-doctrine-mongodb-provider][3].

Example for one connection:

``` {.php}
$app->register(new DoctrineMongoDbProvider(), [
    'mongodb.options' => [
        'server' => 'mongodb://localhost:27017',
        'options' => [
            'username' => 'root',
            'password' => 'root',
            'db' => 'admin'
        ]
    ]
]);
```

Example for multiple connections:

``` {.php}
$app->register(new DoctrineMongoDbProvider(), [
    'mongodbs.options' => [
        'mongo1' => [
            'server' => 'mongodb://localhost:27017',
            'options' => array(
                'username' => 'root',
                'password' => 'root',
                'db' => 'admin'
            ]
        ],
        'mongo2' => [
            'server' => 'mongodb://localhost:27018',
            'options' => [
                'username' => 'root',
                'password' => 'root',
                'db' => 'admin'
            ]
        ]
    ]
));
```

Usage
-----

Example for one connection:

``` {.php}
$document = ['key' => 'value'];

$app['mongodb']
    ->selectDatabase('saxulum-doctrine-mongodb-provider')
    ->selectCollection('sample')
    ->insert($document)
;
```

Example for multiple connections:

``` {.php}
$document = ['key' => 'value'];

$app['mongodbs']['mongo1']
    ->selectDatabase('saxulum-doctrine-mongodb-provider')
    ->selectCollection('sample')
    ->insert($document)
;
```

Copyright
---------
* Dominik Zogg <dominik.zogg@gmail.com>
* Fabien Potencier <fabien@symfony.com> ([DoctrineServiceProvider][4], Logger)
* Kris Wallsmith <kris@symfony.com> (Logger)

[1]: http://silex.sensiolabs.org/
[2]: http://docs.doctrine-project.org/projects/doctrine-mongodb-odm/en/latest/
[3]: https://packagist.org/packages/saxulum/saxulum-doctrine-mongodb-provider
[4]: http://silex.sensiolabs.org/doc/providers/doctrine.html