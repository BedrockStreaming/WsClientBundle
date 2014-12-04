# Bundle WSClient [![Build Status](https://travis-ci.org/M6Web/WsClientBundle.svg?branch=master)](https://travis-ci.org/M6Web/WsClientBundle)

This bundle give a simple webservices client to call external urls. By default, it is based on Guzzle but you can plug any other client library.

## Services

No default service is defined. You must set a configuration to instanciate one or more services.

## Configuration

The main configuration key is `m6_ws_client`. Each subkey defines an instance of a webservice client. These services are named `m6_ws_client_` + the subkey except for the `default` subkey that defines the main service `m6_ws_client`. For each instance, several parameters can be set :

  * `base_url` : the base domain of each url called with the service. If an absolute url is passed to the client, the base url is ignored.
  * `config` (optional) : additional parameters to configure the client, must be an array. See http://guzzle.readthedocs.org/en/latest/clients.html#request-options
  * `cache` (optional) :
    * `ttl` : 86400s by default. Max ttl if force_request_ttl is FALSE, forced ttl if force_request_ttl is TRUE
    * `force_request_ttl` (optional) : FALSE by default. If TRUE, request TTL is the same than the cache TTL, otherwise the request TTL is calculated according to response headers.
    * `service` : low level cache service (must implement M6Web\Bundle\WSClientBundle\Cache\CacheInterface)
    * `adpater` : adapter class name (must implement \Doctrine\Common\Cache\Cache)
    *  storage : (optional) storage class name (must implement \GuzzleHttp\Subscriber\Cache\CacheStorageInterface)
    *  subscriber : (optional) subscriber class (must implement \GuzzleHttp\Subscriber\Cache\SubscriberInterface)
    *  can_cache : (optional) a callable to determine if a request can be cached

Here is an example of a simple configuration :

```yaml
m6_ws_client:
    clients:
        default:
            base_url: 'ws-usine.m6web.fr'
            config:
                timeout: 10
                allow_redirects: {max: 5, strict: false, referer: true}
                exceptions: false
            cache:
                ttl: 3600
                adapter: M6\Bundle\RedisBundle\Guzzle\RedisCacheAdapter
                service: m6_redis
```

## Simple use case

For instance, in a controller :

```php
$wsclient = $this->get('m6_ws_client');
$request = $wsclient->get('http://ws-usine.m6web.fr/parse/?content=my_content');
$response = $request->send();

echo $response->getBody();
```

## unit test

```shell
composer install --dev
./vendor/bin/atoum
```
