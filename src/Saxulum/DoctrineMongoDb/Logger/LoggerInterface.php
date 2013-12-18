<?php

namespace Saxulum\DoctrineMongoDb\Logger;

interface LoggerInterface
{
    public function logQuery(array $query);
}
