<?php

namespace Saxulum\Tests\DoctrineMongoDb\Silex\Provider;

use Doctrine\MongoDB\Connection;
use Saxulum\DoctrineMongoDb\Silex\Provider\DoctrineMongoDbProvider;
use Silex\Application;

/**
 * Class DoctrineMongoDbProviderTest
 *
 * @package Saxulum\Tests\DoctrineMongoDb\Silex\Provider
 */
class DoctrineMongoDbProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * testing single connection configuration read and build
     */
    public function testSingleConnection()
    {
        $app = new Application();
        $app->register(new DoctrineMongoDbProvider());

        /** @var Connection $mongodb */
        $mongodb = $app['mongodb'];
        $this->assertEquals($app['mongodbs']['default'], $mongodb);
        $this->assertInstanceOf('Doctrine\MongoDB\Connection', $mongodb);

        $this->assertEquals($app['mongodbs.config']['default'], $app['mongodb.config']);
        $this->assertInstanceOf('Doctrine\MongoDB\Configuration', $app['mongodb.config']);

        $this->assertEquals($app['mongodbs.event_manager']['default'], $app['mongodb.event_manager']);
        $this->assertInstanceOf('Doctrine\Common\EventManager', $app['mongodb.event_manager']);

        $database = $mongodb->selectDatabase('saxulum-doctrine-mongodb-provider');
        $collection = $database->selectCollection('sample');

        $document = ['key' => 'value'];
        $collection->insert($document);

        $this->assertArrayHasKey('_id', $document);

        $database->dropCollection('sample');
    }

    /**
     * testing multiple connection configuration read and build
     */
    public function testMultipleConnections()
    {
        $app = new Application();
        $app->register(new DoctrineMongoDbProvider(), [
            'mongodbs.options' => [
                'mongo1' => [
                    'server' => 'mongodb://localhost:27017'
                ],
                'mongo2' => [
                    'server' => 'mongodb://localhost:27017'
                ],
            ]
        ]);

        /** @var Connection $mongodb */
        $mongodb = $app['mongodb'];

        $this->assertEquals('mongo1', $app['mongodbs.default']);

        $this->assertEquals($app['mongodbs']['mongo1'], $mongodb);
        $this->assertInstanceOf('Doctrine\MongoDB\Connection', $mongodb);

        $this->assertEquals($app['mongodbs.config']['mongo1'], $app['mongodb.config']);
        $this->assertInstanceOf('Doctrine\MongoDB\Configuration', $app['mongodb.config']);

        $this->assertEquals($app['mongodbs.event_manager']['mongo1'], $app['mongodb.event_manager']);
        $this->assertInstanceOf('Doctrine\Common\EventManager', $app['mongodb.event_manager']);

        $this->assertInstanceOf('Doctrine\MongoDB\Connection', $app['mongodbs']['mongo2']);
        $this->assertInstanceOf('Doctrine\MongoDB\Configuration', $app['mongodbs.config']['mongo2']);
        $this->assertInstanceOf('Doctrine\Common\EventManager', $app['mongodbs.event_manager']['mongo2']);
    }
}
