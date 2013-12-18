<?php

namespace Saxulum\DoctrineMongoDb\Provider;

use Doctrine\Common\EventManager;
use Doctrine\MongoDB\Configuration;
use Doctrine\MongoDB\Connection;
use Saxulum\DoctrineMongoDb\Logger\Logger;

class DoctrineMongoDbProvider
{
    public function register(\Pimple $container)
    {
        $container['mongodb.default_options'] = array(
            'server' => 'mongodb://localhost:27017',
            'options' => array()
            /** @link http://www.php.net/manual/en/mongoclient.construct.php */
        );

        $container['mongodbs.options.initializer'] = $container->protect(function () use ($container) {
            static $initialized = false;

            if ($initialized) {
                return;
            }

            $initialized = true;

            if (!isset($container['mongodbs.options'])) {
                $container['mongodbs.options'] = array('default' => isset($container['mongodb.options']) ? $container['mongodb.options'] : array());
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

        $container['mongodbs'] = $container->share(function ($container) {
            $container['mongodbs.options.initializer']();

            $mongodbs = new \Pimple();
            foreach ($container['mongodbs.options'] as $name => $options) {
                if ($container['mongodbs.default'] === $name) {
                    // we use shortcuts here in case the default has been overridden
                    $config = $container['mongodb.config'];
                    $manager = $container['mongodb.event_manager'];
                } else {
                    $config = $container['mongodbs.config'][$name];
                    $manager = $container['mongodbs.event_manager'][$name];
                }

                $mongodbs[$name] = $mongodbs->share(function () use ($options, $config, $manager) {
                    return new Connection($options['server'], $options['options'], $config, $manager);
                });
            }

            return $mongodbs;
        });

        $container['mongodbs.config'] = $container->share(function ($container) {
            $container['mongodbs.options.initializer']();

            $configs = new \Pimple();
            foreach ($container['mongodbs.options'] as $name => $options) {
                $configs[$name] = new Configuration();

                if (isset($container['logger'])) {
                    $logger = new Logger($container['logger']);
                    $configs[$name]->setLoggerCallable(array($logger,'logQuery'));
                }
            }

            return $configs;
        });

        $container['mongodbs.event_manager'] = $container->share(function ($container) {
            $container['mongodbs.options.initializer']();

            $managers = new \Pimple();
            foreach ($container['mongodbs.options'] as $name => $options) {
                $managers[$name] = new EventManager();
            }

            return $managers;
        });

        // shortcuts for the "first" DB
        $container['mongodb'] = $container->share(function ($container) {
            $mongodbs = $container['mongodbs'];

            return $mongodbs[$container['mongodbs.default']];
        });

        $container['mongodb.config'] = $container->share(function ($container) {
            $mongodbs = $container['mongodbs.config'];

            return $mongodbs[$container['mongodbs.default']];
        });

        $container['mongodb.event_manager'] = $container->share(function ($container) {
            $mongodbs = $container['mongodbs.event_manager'];

            return $mongodbs[$container['mongodbs.default']];
        });
    }
}
