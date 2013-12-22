<?php

namespace Saxulum\Tests\DoctrineMongoDb\Cilex\Provider;

use Doctrine\MongoDB\Connection;
use Saxulum\DoctrineMongoDb\Cilex\Provider\DoctrineMongoDbProvider;
use Cilex\Application;

class DoctrineMongoDbProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testSingleConnection()
    {
        if (!extension_loaded('mongo')) {
            $this->markTestSkipped('mongo is not available');
        }

        $app = new Application('test');
        $app->register(new DoctrineMongoDbProvider());

        /** @var Connection $mongodb */
        $mongodb = $app['mongodb'];

        $this->assertSame($app['mongodbs']['default'], $mongodb);
        $this->assertInstanceOf('Doctrine\MongoDB\Connection', $mongodb);

        $this->assertSame($app['mongodbs.config']['default'], $app['mongodb.config']);
        $this->assertInstanceOf('Doctrine\MongoDB\Configuration', $app['mongodb.config']);

        $this->assertSame($app['mongodbs.event_manager']['default'], $app['mongodb.event_manager']);
        $this->assertInstanceOf('Doctrine\Common\EventManager', $app['mongodb.event_manager']);

        $database = $mongodb->selectDatabase('saxulum-doctrine-mongodb-provider');
        $collection = $database->selectCollection('sample');

        $document = array('key' => 'value');
        $collection->insert($document);

        $this->assertArrayHasKey('_id', $document);

        $database->dropCollection('sample');
    }

    public function testMultipleConnections()
    {
        if (!extension_loaded('mongo')) {
            $this->markTestSkipped('mongo is not available');
        }

        $app = new Application('test');
        $app->register(new DoctrineMongoDbProvider(), array(
            'mongodbs.options' => array(
                'mongo1' => array(
                    'server' => 'mongodb://localhost:27017'
                ),
                'mongo2' => array(
                    'server' => 'mongodb://localhost:27017'
                ),
            )
        ));

        /** @var Connection $mongodb */
        $mongodb = $app['mongodb'];

        $this->assertSame('mongo1', $app['mongodbs.default']);

        $this->assertSame($app['mongodbs']['mongo1'], $mongodb);
        $this->assertInstanceOf('Doctrine\MongoDB\Connection', $mongodb);

        $this->assertSame($app['mongodbs.config']['mongo1'], $app['mongodb.config']);
        $this->assertInstanceOf('Doctrine\MongoDB\Configuration', $app['mongodb.config']);

        $this->assertSame($app['mongodbs.event_manager']['mongo1'], $app['mongodb.event_manager']);
        $this->assertInstanceOf('Doctrine\Common\EventManager', $app['mongodb.event_manager']);

        $this->assertInstanceOf('Doctrine\MongoDB\Connection', $app['mongodbs']['mongo2']);
        $this->assertInstanceOf('Doctrine\MongoDB\Configuration', $app['mongodbs.config']['mongo2']);
        $this->assertInstanceOf('Doctrine\Common\EventManager', $app['mongodbs.event_manager']['mongo2']);
    }
}
