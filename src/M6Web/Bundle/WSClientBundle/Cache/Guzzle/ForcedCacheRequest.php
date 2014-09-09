<?php

namespace M6Web\Bundle\WSClientBundle\Cache\Guzzle;

use GuzzleHttp\Message\RequestInterface;

/**
 * Forced cache plugin
 */
class ForcedCacheRequest
{
    /**
     * {@inheritdoc}
     */
    public static function canCacheRequest(RequestInterface $request)
    {
        return true;
    }
}