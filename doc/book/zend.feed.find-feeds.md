# Retrieving Feeds from Web Pages

## Find Feed Links

Web pages often contain **&lt;link&gt;** tags that refer to feeds with content relevant to the
particular page. `Laminas\Feed\Reader\Reader` enables you to retrieve all feeds referenced by a web
page with one simple method call:

```php
$feedLinks = Laminas\Feed\Reader\Reader::findFeedLinks('http://www.example.com/news.html');
```

> ## Finding feed links requires an HTTP client
>
> To find feed links, you will need to have an [HTTP client](laminas.feed.http-clients)
> available. 
>
> If you are not using laminas-http, you will need to inject `Reader` with the HTTP
> client. See the [section on providing a client to Reader](laminas.feed.http-clients#providing-a-client-to-reader).

Here the `findFeedLinks()` method returns a `Laminas\Feed\Reader\FeedSet` object, that is in turn, a
collection of other `Laminas\Feed\Reader\FeedSet` objects, that are referenced by **&lt;link&gt;** tags
on the `news.html` web page. `Laminas\Feed\Reader\Reader` will throw a
`Laminas\Feed\Reader\Exception\RuntimeException` upon failure, such as an *HTTP* 404 response code or a
malformed feed.

You can examine all feed links located by iterating across the collection:

```php
$rssFeed = null;
$feedLinks = Laminas\Feed\Reader\Reader::findFeedLinks('http://www.example.com/news.html');
foreach ($feedLinks as $link) {
    if (stripos($link['type'], 'application/rss+xml') !== false) {
        $rssFeed = $link['href'];
        break;
}
```

Each `Laminas\Feed\Reader\FeedSet` object will expose the rel, href, type and title properties of
detected links for all *RSS*, *Atom* or *RDF* feeds. You can always select the first encountered
link of each type by using a shortcut:

```php
$rssFeed = null;
$feedLinks = Laminas\Feed\Reader\Reader::findFeedLinks('http://www.example.com/news.html');
$firstAtomFeed = $feedLinks->atom;
```
