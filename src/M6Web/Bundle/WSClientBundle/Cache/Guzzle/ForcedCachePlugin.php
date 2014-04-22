<?php

namespace M6Web\Bundle\WSClientBundle\Cache\Guzzle;

use Guzzle\Plugin\Cache\CachePlugin as GuzzleCachePlugin;
use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Message\Response;

/**
 * Forced cache plugin
 */
class ForcedCachePlugin extends GuzzleCachePlugin
{
    /**
     * {@inheritdoc}
     */
    public function canResponseSatisfyRequest(RequestInterface $request, Response $response)
    {
        return true;
    }
}