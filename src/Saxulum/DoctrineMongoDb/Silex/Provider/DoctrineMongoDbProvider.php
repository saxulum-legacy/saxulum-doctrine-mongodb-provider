<?php

namespace Saxulum\DoctrineMongoDb\Silex\Provider;

use Saxulum\DoctrineMongoDb\Provider\DoctrineMongoDbProvider as BaseDoctrineMongoDbProvider;
use Silex\Application;
use Silex\ServiceProviderInterface;

class DoctrineMongoDbProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function boot(Application $app)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function register(Application $app)
    {
        $pimpleServiceProvider = new BaseDoctrineMongoDbProvider;
        $pimpleServiceProvider->register($app);
    }
}
