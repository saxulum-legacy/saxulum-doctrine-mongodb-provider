<?php

namespace Saxulum\DoctrineMongoDb\Provider;

use Doctrine\Common\EventManager;
use Doctrine\MongoDB\Configuration;
use Doctrine\MongoDB\Connection;
use Saxulum\DoctrineMongoDb\Logger\Logger;

class DoctrineMongoDbProvider
{
    public function register(\Pimple $app)
    {
        $app['mongodb.default_options'] = array(
            'server' => 'mongodb://localhost:27017',
            'options' => array(
                'connect' => true,
            )
            /** @link http://www.php.net/manual/en/mongoclient.construct.php */
        );

        $app['mongodbs.options.initializer'] = $app->protect(function () use ($app) {
            static $initialized = false;

            if ($initialized) {
                return;
            }

            $initialized = true;

            if (!isset($app['mongodbs.options'])) {
                $app['mongodbs.options'] = array('default' => isset($app['mongodb.options']) ? $app['mongodb.options'] : array());
            }

            $tmp = $app['mongodbs.options'];
            foreach ($tmp as $name => &$options) {
                $options = array_replace_recursive($app['mongodb.default_options'], $options);

                if (!isset($app['mongodbs.default'])) {
                    $app['mongodbs.default'] = $name;
                }
            }
            $app['mongodbs.options'] = $tmp;
        });

        $app['mongodbs'] = $app->share(function ($app) {
            $app['mongodbs.options.initializer']();

            $mongodbs = new \Pimple();
            foreach ($app['mongodbs.options'] as $name => $options) {
                if ($app['mongodbs.default'] === $name) {
                    // we use shortcuts here in case the default has been overridden
                    $config = $app['mongodb.config'];
                    $manager = $app['mongodb.event_manager'];
                } else {
                    $config = $app['mongodbs.config'][$name];
                    $manager = $app['mongodbs.event_manager'][$name];
                }

                $mongodbs[$name] = $mongodbs->share(function () use ($options, $config, $manager) {
                    return new Connection($options['server'], $options['options'], $config, $manager);
                });
            }

            return $mongodbs;
        });

        $app['mongodbs.config'] = $app->share(function ($app) {
            $app['mongodbs.options.initializer']();

            $configs = new \Pimple();
            foreach ($app['mongodbs.options'] as $name => $options) {
                $configs[$name] = new Configuration();

                if (isset($app['logger']) && class_exists('Symfony\Component\HttpKernel\Log\LoggerInterface')) {
                    $logger = new Logger($app['logger']);
                    $configs[$name]->setLoggerCallable(array('','logQuery'));
                }
            }

            return $configs;
        });

        $app['mongodbs.event_manager'] = $app->share(function ($app) {
            $app['mongodbs.options.initializer']();

            $managers = new \Pimple();
            foreach ($app['mongodbs.options'] as $name => $options) {
                $managers[$name] = new EventManager();
            }

            return $managers;
        });

        // shortcuts for the "first" DB
        $app['mongodb'] = $app->share(function ($app) {
            $mongodbs = $app['mongodbs'];

            return $mongodbs[$app['mongodbs.default']];
        });

        $app['mongodb.config'] = $app->share(function ($app) {
            $mongodbs = $app['mongodbs.config'];

            return $mongodbs[$app['mongodbs.default']];
        });

        $app['mongodb.event_manager'] = $app->share(function ($app) {
            $mongodbs = $app['mongodbs.event_manager'];

            return $mongodbs[$app['mongodbs.default']];
        });
    }
}