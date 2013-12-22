saxulum-doctrine-mongodb-provider
=================================

**works with plain silex-php**

[![Build Status](https://api.travis-ci.org/saxulum/saxulum-doctrine-mongodb-provider.png?branch=master)](https://travis-ci.org/saxulum/saxulum-doctrine-mongodb-provider)
[![Total Downloads](https://poser.pugx.org/saxulum/saxulum-doctrine-mongodb-provider/downloads.png)](https://packagist.org/packages/saxulum/saxulum-doctrine-mongodb-provider)
[![Latest Stable Version](https://poser.pugx.org/saxulum/saxulum-doctrine-mongodb-provider/v/stable.png)](https://packagist.org/packages/saxulum/saxulum-doctrine-mongodb-provider)

Features
--------

* Support for mongodb within [Silex][1] or [Cilex][2], it does NOT PROVIDE the [ODM][3] integration

Requirements
------------

 * PHP 5.3+
 * Doctrine Mongodb 1.0 Beta+

Installation
------------

Through [Composer](http://getcomposer.org) as [saxulum/saxulum-doctrine-mongodb-provider][4].

Example for one connection:

``` {.php}
$app->register(new DoctrineMongoDbProvider(), array(
    'mongodb.options' => array(
        'server' => 'mongodb://localhost:27017',
        'options' => array(
            'username' => 'root',
            'password' => 'root',
            'db' => 'admin'
        )
    )
));
```

Example for multiple connections:

``` {.php}
$app->register(new DoctrineMongoDbProvider(), array(
    'mongodbs.options' => array(
        'mongo1' => array(
            'server' => 'mongodb://localhost:27017',
            'options' => array(
                'username' => 'root',
                'password' => 'root',
                'db' => 'admin'
            )
        ),
        'mongo2' => array(
            'server' => 'mongodb://localhost:27018',
            'options' => array(
                'username' => 'root',
                'password' => 'root',
                'db' => 'admin'
            )
        )
    )
));
```

Usage
-----

Example for one connection:

``` {.php}
$document = array('key' => 'value');

$app['mongodb']
    ->selectDatabase('saxulum-doctrine-mongodb-provider')
    ->selectCollection('sample')
    ->insert($document)
;
```

Example for multiple connections:

``` {.php}
$document = array('key' => 'value');

$app['mongodbs']['mongo1']
    ->selectDatabase('saxulum-doctrine-mongodb-provider')
    ->selectCollection('sample')
    ->insert($document)
;
```

Copyright
---------
* Dominik Zogg <dominik.zogg@gmail.com>
* Fabien Potencier <fabien@symfony.com> ([DoctrineServiceProvider][5], Logger)
* Kris Wallsmith <kris@symfony.com> (Logger)

[1]: http://silex.sensiolabs.org/
[2]: http://cilex.github.io/
[3]: http://docs.doctrine-project.org/projects/doctrine-mongodb-odm/en/latest/
[4]: https://packagist.org/packages/saxulum/saxulum-doctrine-mongodb-provider
[5]: http://silex.sensiolabs.org/doc/providers/doctrine.html