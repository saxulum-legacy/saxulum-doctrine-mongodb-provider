<?php

namespace Saxulum\DoctrineMongoDb\Logger;

/**
 * Interface LoggerInterface
 *
 * @package Saxulum\DoctrineMongoDb\Logger
 */
interface LoggerInterface
{
    /**
     * @param array $query
     * 
     * @return mixed
     */
    public function logQuery(array $query);
}
