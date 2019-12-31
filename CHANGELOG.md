# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 2.6.0 - 2015-11-24

### Added

- [zendframework/zend-feed#13](https://github.com/zendframework/zend-feed/pull/13) introduces
  `Laminas\Feed\Writer\StandaloneExtensionManager`, an implementation of
  `Laminas\Feed\Writer\ExtensionManagerInterface` that has no dependencies.
  `Laminas\Feed\Writer\ExtensionManager` now composes this by default, instead of
  `Laminas\Feed\Writer\ExtensionPluginManager`, for managing the various feed and
  entry extensions. If you relied on `ExtensionPluginManager` previously, you
  will need to create an instance manually and inject it into the `Writer`
  instance.
- [zendframework/zend-feed#14](https://github.com/zendframework/zend-feed/pull/14) introduces:
  - `Laminas\Feed\Reader\Http\HeaderAwareClientInterface`, which extends
    `ClientInterface` and adds an optional argument to the `get()` method,
    `array $headers = []`; this argument allows specifying request headers for
    the client to send. `$headers` should have header names for keys, and the
    values should be arrays of strings/numbers representing the header values
    (if only a single value is necessary, it should be represented as an single
    value array).
  - `Laminas\Feed\Reader\Http\HeaderAwareResponseInterface`, which extends
    `ResponseInterface` and adds the method `getHeader($name, $default = null)`.
    Clients may return either a `ResponseInterface` or
    `HeaderAwareResponseInterface` instance.
  - `Laminas\Feed\Reader\Http\Response`, which is an implementation of
    `HeaderAwareResponseInterface`. Its constructor accepts the status code,
    body, and, optionally, headers.
  - `Laminas\Feed\Reader\Http\Psr7ResponseDecorator`, which is an implementation of
    `HeaderAwareResponseInterface`. Its constructor accepts a PSR-7 response
    instance, and the various methdos then proxy to those methods. This should
    make creating wrappers for PSR-7 HTTP clients trivial.
  - `Laminas\Feed\Reader\Http\LaminasHttpClientDecorator`, which decorates a
    `Laminas\Http\Client` instance, implements `HeaderAwareClientInterface`, and
    returns a `Response` instance seeded from the laminas-http response upon
    calling `get()`. The class exposes a `getDecoratedClient()` method to allow
    retrieval of the decorated laminas-http client instance.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-feed#5](https://github.com/zendframework/zend-feed/pull/5) fixes the enclosure
  length check to allow zero and integer strings.
- [zendframework/zend-feed#2](https://github.com/zendframework/zend-feed/pull/2) ensures that the
  routine for "absolutising" a link in `Reader\FeedSet` always generates a URI
  with a scheme.
- [zendframework/zend-feed#14](https://github.com/zendframework/zend-feed/pull/14) makes the following
  changes to fix behavior around HTTP clients used within
  `Laminas\Feed\Reader\Reader`:
  - `setHttpClient()` now ensures that the passed client is either a
    `Laminas\Feed\Reader\Http\ClientInterface` or `Laminas\Http\Client`, raising an
    `InvalidArgumentException` if neither. If a `Laminas\Http\Client` is passed, it
    is passed to the constructor of `Laminas\Feed\Reader\Http\LaminasHttpClientDecorator`,
    and the decorator instance is used.
  - `getHttpClient()` now *always* returns a `Laminas\Feed\Reader\Http\ClientInterface`
    instance. If no instance is currently registered, it lazy loads a
    `LaminasHttpClientDecorator` instance.
  - `import()` was updated to consume a `ClientInterface` instance; when caches
    are in play, it checks the client against `HeaderAwareClientInterface` to
    determine if it can check for HTTP caching headers, and, if so, to retrieve
    them.
  - `findFeedLinks()` was updated to consume a `ClientInterface`.
