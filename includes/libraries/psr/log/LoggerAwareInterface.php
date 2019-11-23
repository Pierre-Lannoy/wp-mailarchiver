<?php

namespace Psr\Log;

/**
 * Describes an archiver-aware instance.
 */
interface ArchiverAwareInterface
{
    /**
     * Sets an archiver instance on the object.
     *
     * @param ArchiverInterface $archiver
     *
     * @return void
     */
    public function setArchiver(ArchiverInterface $archiver);
}
