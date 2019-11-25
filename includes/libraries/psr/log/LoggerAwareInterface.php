<?php

namespace Psr\Log;

/**
 * Describes an logger-aware instance.
 */
interface LoggerAwareInterface
{
    /**
     * Sets an logger instance on the object.
     *
     * @param LoggerInterface $logger
     *
     * @return void
     */
    public function setLogger(LoggerInterface $logger);
}
