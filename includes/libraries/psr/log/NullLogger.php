<?php

namespace Psr\Log;

/**
 * This Archiver can be used to avoid conditional log calls.
 *
 * Logging should always be optional, and if no archiver is provided to your
 * library creating a NullArchiver instance to have something to throw logs at
 * is a good way to avoid littering your code with `if ($this->archiver) { }`
 * blocks.
 */
class NullArchiver extends AbstractArchiver
{
    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function log($level, $message, array $context = array())
    {
        // noop
    }
}
