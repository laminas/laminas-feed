# Introduction

`Laminas\Feed` provides functionality for consuming RSS and Atom feeds. It provides
a natural syntax for accessing elements of feeds, feed attributes, and entry
attributes. `Laminas\Feed` also has extensive support for modifying feed and entry
structure with the same natural syntax, and turning the result back into XML.
In the future, this modification support could provide support for the Atom
Publishing Protocol.

`Laminas\Feed` consists of `Laminas\Feed\Reader` for reading RSS and Atom feeds,
`Laminas\Feed\Writer` for writing RSS and Atom feeds, and `Laminas\Feed\PubSubHubbub`
for working with Hub servers. Furthermore, both `Laminas\Feed\Reader` and
`Laminas\Feed\Writer` support extensions which allows for working with additional
data in feeds, not covered in the core API but used in conjunction with RSS and
Atom feeds.

In the example below, we demonstrate a simple use case of retrieving an RSS feed
and saving relevant portions of the feed data to a simple PHP array, which could
then be used for printing the data, storing to a database, etc.

> ### RSS optional properties
>
> Many *RSS* feeds have different channel and item properties available. The
> *RSS* specification provides for many optional properties, so be aware of this
> when writing code to work with *RSS* data. `Laminas\Feed` supports all optional
> properties of the core *RSS* and *Atom* specifications.

## Reading RSS Feed Data

```php
// Fetch the latest Slashdot headlines
try {
    $slashdotRss =
        Laminas\Feed\Reader\Reader::import('http://rss.slashdot.org/Slashdot/slashdot');
} catch (Laminas\Feed\Reader\Exception\RuntimeException $e) {
    // feed import failed
    echo "Exception caught importing feed: {$e->getMessage()}\n";
    exit;
}

// Initialize the channel/feed data array
$channel = [
    'title'       => $slashdotRss->getTitle(),
    'link'        => $slashdotRss->getLink(),
    'description' => $slashdotRss->getDescription(),
    'items'       => [],
];

// Loop over each channel item/entry and store relevant data for each
foreach ($slashdotRss as $item) {
    $channel['items'][] = [
        'title'       => $item->getTitle(),
        'link'        => $item->getLink(),
        'description' => $item->getDescription(),
    ];
}
```

Your `$channel` array now contains the basic meta-information for the RSS
channel and all items that it contained. The process is identical for Atom
feeds since `Laminas\Feed` provides a common feed API; i.e. all getters and
setters are the same regardless of feed format.
