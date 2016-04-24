<?php

namespace Saxulum\DoctrineMongoDb\Cilex\Provider;

use Saxulum\DoctrineMongoDb\Provider\DoctrineMongoDbProvider as BaseDoctrineMongoDbProvider;
use Cilex\Application;
use Cilex\ServiceProviderInterface;

/**
 * Class DoctrineMongoDbProvider
 * 
 * @package Saxulum\DoctrineMongoDb\Cilex\Provider
 */
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
