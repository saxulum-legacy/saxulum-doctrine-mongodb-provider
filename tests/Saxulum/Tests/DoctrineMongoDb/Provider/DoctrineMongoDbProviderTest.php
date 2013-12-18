<?php

namespace Saxulum\Tests\DoctrineMongoDb\Provider;

use Doctrine\MongoDB\Connection;
use Saxulum\DoctrineMongoDb\Silex\Provider\DoctrineMongoDbProvider;
use Silex\Application;

class DoctrineMongoDbProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testSingleConnection()
    {
        if(!extension_loaded('mongo')) {
            $this->markTestSkipped('mongo is not available');
        }

        $app = new Application();
        $app->register(new DoctrineMongoDbProvider(), array(
            'mongodb.options' => array(
                'options' => array(
                    'username' => 'root',
                    'password' => 'root',
                    'db' => 'admin'
                )
            )
        ));

        /** @var Connection $mongodb */
        $mongodb = $app['mongodb'];

        $this->assertSame($app['mongodbs']['default'], $mongodb);

        $database = $mongodb->selectDatabase('saxulum-doctrine-mongodb-provider');
        $collection = $database->selectCollection('sample');

        $document = array('key' => 'value');
        $collection->insert($document);

        $this->assertArrayHasKey('_id', $document);

        $database->dropCollection('sample');
    }
}