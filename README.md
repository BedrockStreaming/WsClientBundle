# Bundle WSClient

This bundle give a simple webservices client to call external urls. By default, it is based on Guzzle but you can plug any other client library.

## Services

No default service is defined. You must set a configuration to instanciate one or more services.

## Configuration

The main configuration key is `m6_ws_client`. Each subkey defines an instance of a webservice client. These services are named `m6_ws_client_` + the subkey except for the `default` subkey that defines the main service `m6_ws_client`. For each instance, several parameters can be set :

  * `base_url` : the base domain of each url called with the service. If an absolute url is passed to the client, the base url is ignored.
  * `config` (optional) : additional parameters to configure the client, must be an array. With Guzzle, you can define a timeout (1s by default).
    * `timeout` : Request timeout, 1 (second) by default (int)
    * `followlocation` : Follow _location_ header (boolean)
    * `maxredirs` : Max redirections (int)
    * `exceptions` : Throw exceptions on http errors (boolean)
  * `cache` (optional) :
    * `ttl` : 86400s by default
    * `force_request_ttl` (optional) : FALSE by default. If TRUE, request TTL is the same than the cache TTL, otherwise the request TTL is calculated according to response headers.
    * `adpater` : (M6\Bundle\FrontBundle\Common\Cache\RedisCacheAdapter for example)
    * `service` : (m6_redis for example)
    * `resetter` (optional) :
      * `service` : service responsible for cache clearing
      * `query_param` : query parameter to add to the called url to clear server cache

Here is an example of a simple configuration :

```yaml
m6_ws_client:
    default:
        base_url: 'ws-usine.m6web.fr'
        config:
            timeout: 1
            followlocation: true
            maxredirs: 5
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