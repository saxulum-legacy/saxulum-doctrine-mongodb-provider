<?php

namespace Saxulum\DoctrineMongoDb\Provider;

use Pimple\Container;
use Doctrine\Common\EventManager;
use Doctrine\MongoDB\Configuration;
use Doctrine\MongoDB\Connection;
use Saxulum\DoctrineMongoDb\Logger\Logger;

/**
 * Class DoctrineMongoDbProvider
 *
 * @package Saxulum\DoctrineMongoDb\Provider
 */
class DoctrineMongoDbProvider
{
    /**
     * @param Container $container
     */
    public function register(Container $container)
    {
        /** @link http://www.php.net/manual/en/mongoclient.construct.php */
        $container['mongodb.default_options'] = [
            'server' => 'mongodb://localhost:27017',
            'options' => []
        ];

        $container['mongodbs.options.initializer'] = $container->protect(function () use ($container) {
            static $initialized = false;

            if ($initialized) {
                return;
            }

            $initialized = true;

            if (!isset($container['mongodbs.options'])) {
                $container['mongodbs.options'] = ['default' => isset($container['mongodb.options']) ? $container['mongodb.options'] : []];
            }

            $tmp = $container['mongodbs.options'];
            foreach ($tmp as $name => &$options) {
                $options = array_replace_recursive($container['mongodb.default_options'], $options);

                if (!isset($container['mongodbs.default'])) {
                    $container['mongodbs.default'] = $name;
                }
            }
            $container['mongodbs.options'] = $tmp;
        });

        $container['mongodbs'] = function ($container) {
            $container['mongodbs.options.initializer']();

            $mongodbs = new Container();
            foreach ($container['mongodbs.options'] as $name => $options) {
                if ($container['mongodbs.default'] === $name) {
                    // we use shortcuts here in case the default has been overridden
                    $config = $container['mongodb.config'];
                    $manager = $container['mongodb.event_manager'];
                } else {
                    $config = $container['mongodbs.config'][$name];
                    $manager = $container['mongodbs.event_manager'][$name];
                }

                $mongodbs[$name] = function () use ($options, $config, $manager) {
                    return new Connection($options['server'], $options['options'], $config, $manager);
                };
            }

            return $mongodbs;
        };

        $container['mongodbs.config'] = function ($container) {
            $container['mongodbs.options.initializer']();

            $configs = new Container();
            foreach ($container['mongodbs.options'] as $name => $options) {
                $configs[$name] = new Configuration();

                if (isset($container['logger'])) {
                    $logger = new Logger($container['logger']);
                    $configs[$name]->setLoggerCallable([$logger,'logQuery']);
                }
            }

            return $configs;
        };

        $container['mongodbs.event_manager'] = function ($container) {
            $container['mongodbs.options.initializer']();

            $managers = new Container();
            foreach ($container['mongodbs.options'] as $name => $options) {
                $managers[$name] = new EventManager();
            }

            return $managers;
        };

        // shortcuts for the "first" DB
        $container['mongodb'] = function ($container) {
            $mongodbs = $container['mongodbs'];

            return $mongodbs[$container['mongodbs.default']];
        };

        $container['mongodb.config'] = function ($container) {
            $mongodbs = $container['mongodbs.config'];

            return $mongodbs[$container['mongodbs.default']];
        };

        $container['mongodb.event_manager'] = function ($container) {
            $mongodbs = $container['mongodbs.event_manager'];

            return $mongodbs[$container['mongodbs.default']];
        };
    }
}
