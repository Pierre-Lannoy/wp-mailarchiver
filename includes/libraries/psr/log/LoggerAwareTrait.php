<?php

namespace Psr\Log;

/**
 * Basic Implementation of ArchiverAwareInterface.
 */
trait ArchiverAwareTrait
{
    /**
     * The archiver instance.
     *
     * @var ArchiverInterface
     */
    protected $archiver;

    /**
     * Sets an archiver.
     *
     * @param ArchiverInterface $archiver
     */
    public function setArchiver(ArchiverInterface $archiver)
    {
        $this->archiver = $archiver;
    }
}
