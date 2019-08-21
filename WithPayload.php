<?php

namespace sorokinmedia\rollbar;

/**
 * Interface WithPayload
 * @package sorokinmedia\rollbar
 */
interface WithPayload
{
    /**
     * @return array|null Payload data to be sent to Rollbar
     */
    public function rollbarPayload(): ?array;
}
