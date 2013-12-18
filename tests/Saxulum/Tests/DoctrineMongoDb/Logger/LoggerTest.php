<?php

namespace Saxulum\Tests\DoctrineMongoDb\Loggger;

use Psr\Log\AbstractLogger;
use Saxulum\DoctrineMongoDb\Logger\Logger;

class LoggerTest extends \PHPUnit_Framework_TestCase
{
    public function testLog()
    {
        $testLogger = new TestLogger();

        $logger = new Logger($testLogger);
        $logger->logQuery(array(
            'insert' => array('key' => 'value')
        ));

        $logEntries = $testLogger->getLogEntries();
        $this->assertEquals('MongoDB query: {"insert":{"key":"value"}}', $logEntries['info'][0]);
    }
}

class TestLogger extends AbstractLogger
{
    /**
     * @var array
     */
    protected $logEntries;

    /**
     * Logs with an arbitrary level.
     *
     * @param  mixed  $level
     * @param  string $message
     * @param  array  $context
     * @return null
     */
    public function log($level, $message, array $context = array())
    {
        $this->logEntries[$level][] = $message;
    }

    /**
     * @return array
     */
    public function getLogEntries()
    {
        return $this->logEntries;
    }
}
