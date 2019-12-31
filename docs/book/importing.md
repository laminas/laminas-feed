# Importing Feeds

`Laminas\Feed` enables developers to retrieve feeds via `Laminas\Feader\Reader`. If
you know the URI of a feed, use the `Laminas\Feed\Reader\Reader::import()` method
to consume it:

```php
$feed = Laminas\Feed\Reader\Reader::import('http://feeds.example.com/feedName');
```

> ### Importing requires an HTTP client
>
> To import a feed, you will need to have an [HTTP client](http-clients.md)
> available.
>
> If you are not using laminas-http, you will need to inject `Reader` with the HTTP
> client. See the [section on providing a client to Reader](http-clients.md#providing-a-client-to-reader).

You can also use `Laminas\Feed\Reader\Reader` to fetch the contents of a feed from
a file or the contents of a PHP string variable:

```php
// importing a feed from a text file
$feedFromFile = Laminas\Feed\Reader\Reader::importFile('feed.xml');

// importing a feed from a PHP string variable
$feedFromPHP = Laminas\Feed\Reader\Reader::importString($feedString);
```

In each of the examples above, an object of a class that extends
`Laminas\Feed\Reader\Feed\AbstractFeed` is returned upon success, depending on the
type of the feed. If an RSS feed were retrieved via one of the import methods
above, then a `Laminas\Feed\Reader\Feed\Rss` object would be returned. On the other
hand, if an Atom feed were imported, then a `Laminas\Feed\Reader\Feed\Atom` object
is returned. The import methods will also throw a
`Laminas\Feed\Exception\Reader\RuntimeException` object upon failure, such as an
unreadable or malformed feed.

## Dumping the contents of a feed

To dump the contents of a `Laminas\Feed\Reader\Feed\AbstractFeed` instance, you may
use the `saveXml()` method.

```php
assert($feed instanceof Laminas\Feed\Reader\Feed\AbstractFeed);

// dump the feed to standard output
print $feed->saveXml();
```
