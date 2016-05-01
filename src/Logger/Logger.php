<?php

namespace Saxulum\DoctrineMongoDb\Logger;

use Psr\Log\LoggerInterface as PsrLoggerInterface;

/**
 * Class Logger
 * 
 * @package Saxulum\DoctrineMongoDb\Logger
 */
class Logger implements LoggerInterface
{
    private $logger;
    private $prefix;
    private $batchInsertThreshold;

    /**
     * Logger constructor.
     *
     * @param PsrLoggerInterface|null $logger
     * @param string                  $prefix
     */
    public function __construct(PsrLoggerInterface $logger = null, $prefix = 'MongoDB query: ')
    {
        $this->logger = $logger;
        $this->prefix = $prefix;
    }

    /**
     * @param $batchInsertThreshold
     */
    public function setBatchInsertThreshold($batchInsertThreshold)
    {
        $this->batchInsertThreshold = $batchInsertThreshold;
    }

    /**
     * @param array $query
     *
     * @return null
     */
    public function logQuery(array $query)
    {
        if (null === $this->logger) {
            return;
        }

        if (isset($query['batchInsert']) && null !== $this->batchInsertThreshold && $this->batchInsertThreshold <= $query['num']) {
            $query['data'] = '**'.$query['num'].' item(s)**';
        }

        array_walk_recursive($query, function (&$value, $key) {
            if ($value instanceof \MongoBinData) {
                $value = base64_encode($value->bin);

                return;
            }
            if (is_float($value) && is_infinite($value)) {
                $value = ($value < 0 ? '-' : '') . 'Infinity';

                return;
            }
            if (is_float($value) && is_nan($value)) {
                $value = 'NaN';

                return;
            }
        });

        $this->logger->info($this->prefix.json_encode($query));
    }
}
