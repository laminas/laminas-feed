# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 2.9.1 - 2018-05-14

### Added

- Nothing.

### Changed

- [zendframework/zend-feed#16](https://github.com/zendframework/zend-feed/pull/16) updates the `Laminas\Feed\Pubsubhubbub\AbstractCallback` to no longer use the
  `$GLOBALS['HTTP_RAW_POST_DATA']` value as a fallback when `php://input` is
  empty. The fallback existed because, prior to PHP 5.6, `php://input` could
  only be read once. As we now require PHP 5.6, the fallback is unnecessary,
  and best removed as the globals value is deprecated.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-feed#68](https://github.com/zendframework/zend-feed/pull/68) fixes the behavior of `Laminas\Feed\Writer\AbstractFeed::setTitle()` and
  `Laminas\Feed\Writer\Entry::setTitle()` to accept the string `"0"`.

- [zendframework/zend-feed#68](https://github.com/zendframework/zend-feed/pull/68) updates both `Laminas\Feed\Writer\AbstractFeed` and `Laminas\Feed\Writer\Entry`
  to no longer throw an exception for entry titles which have a string value of `0`.

## 2.9.0 - 2017-12-04

### Added

- [zendframework/zend-feed#52](https://github.com/zendframework/zend-feed/pull/52) adds support for PHP
  7.2

- [zendframework/zend-feed#53](https://github.com/zendframework/zend-feed/pull/53) adds a number of
  additional aliases to the `Writer\ExtensionPluginManager` to ensure plugins
  will be pulled as expected.

- [zendframework/zend-feed#63](https://github.com/zendframework/zend-feed/pull/63) adds the feed title
  to the attributes incorporated in the `FeedSet` instance, per what was already
  documented.

- [zendframework/zend-feed#55](https://github.com/zendframework/zend-feed/pull/55) makes two API
  additions to the `StandaloneExtensionManager` implementations of both the reader
  and writer subcomponents:

  - `$manager->add($name, $class)` will add an extension class using the
    provided name.
  - `$manager->remove($name)` will remove an existing extension by the provided
    name.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- [zendframework/zend-feed#52](https://github.com/zendframework/zend-feed/pull/52) removes support for
  HHVM.

### Fixed

- [zendframework/zend-feed#50](https://github.com/zendframework/zend-feed/pull/50) fixes a few issues
  in the PubSubHubbub `Subscription` model where counting was being performed on
  uncountable data; this ensures the subcomponent will work correctly under PHP
  7.2.

## 2.8.0 - 2017-04-02

### Added

- [zendframework/zend-feed#27](https://github.com/zendframework/zend-feed/pull/27) adds a documentation
  chapter demonstrating wrapping a PSR-7 client to use with `Laminas\Feed\Reader`.
- [zendframework/zend-feed#22](https://github.com/zendframework/zend-feed/pull/22) adds missing
  ExtensionManagerInterface on Writer\ExtensionPluginManager.
- [zendframework/zend-feed#32](https://github.com/zendframework/zend-feed/pull/32) adds missing
  ExtensionManagerInterface on Reader\ExtensionPluginManager.

### Deprecated

- Nothing.

### Removed

- [zendframework/zend-feed#38](https://github.com/zendframework/zend-feed/pull/38) dropped php 5.5
  support

### Fixed

- [zendframework/zend-feed#35](https://github.com/zendframework/zend-feed/pull/35) fixed
  "A non-numeric value encountered" in php 7.1
- [zendframework/zend-feed#39](https://github.com/zendframework/zend-feed/pull/39) fixed protocol
  relative link absolutisation
- [zendframework/zend-feed#40](https://github.com/zendframework/zend-feed/pull/40) fixed service
  manager v3 compatibility aliases in extension plugin managers

## 2.7.0 - 2016-02-11

### Added

- [zendframework/zend-feed#21](https://github.com/zendframework/zend-feed/pull/21) edits, revises, and
  prepares the documentation for publication at https://docs.laminas.dev/laminas-feed/

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-feed#20](https://github.com/zendframework/zend-feed/pull/20) makes the two
  laminas-servicemanager extension manager implementations forwards compatible
  with version 3, and the overall code base forwards compatible with laminas-stdlib
  v3.

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
