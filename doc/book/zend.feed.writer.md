# Laminas\\Feed\\Writer\\Writer

## Introduction

`Laminas\Feed\Writer\Writer` is the sibling component to `Laminas\Feed\Reader\Reader` responsible for
generating feeds for output. It supports the Atom 1.0 specification (*RFC* 4287) and *RSS* 2.0 as
specified by the *RSS* Advisory Board (*RSS* 2.0.11). It does not deviate from these standards. It
does, however, offer a simple Extension system which allows for any extension and module for either
of these two specifications to be implemented if they are not provided out of the box.

In many ways, `Laminas\Feed\Writer\Writer` is the inverse of `Laminas\Feed\Reader\Reader`. Where
`Laminas\Feed\Reader\Reader` focuses on providing an easy to use architecture fronted by getter
methods, `Laminas\Feed\Writer\Writer` is fronted by similarly named setters or mutators. This ensures
the *API* won't pose a learning curve to anyone familiar with `Laminas\Feed\Reader\Reader`.

As a result of this design, the rest may even be obvious. Behind the scenes, data set on any
`Laminas\Feed\Writer\Writer` Data Container object is translated at render time onto a DOMDocument
object using the necessary feed elements. For each supported feed type there is both an Atom 1.0 and
*RSS* 2.0 renderer. Using a DOMDocument class rather than a templating solution has numerous
advantages, the most obvious being the ability to export the DOMDocument for additional processing
and relying on *PHP* *DOM* for correct and valid rendering.

## Architecture

The architecture of `Laminas\Feed\Writer\Writer` is very simple. It has two core sets of classes: data
containers and renderers.

The containers include the `Laminas\Feed\Writer\Feed` and `Laminas\Feed\Writer\Entry` classes. The Entry
classes can be attached to any Feed class. The sole purpose of these containers is to collect data
about the feed to generate using a simple interface of setter methods. These methods perform some
data validity testing. For example, it will validate any passed *URI*s, dates, etc. These checks are
not tied to any of the feed standards definitions. The container objects also contain methods to
allow for fast rendering and export of the final feed, and these can be reused at will.

In addition to the main data container classes, there are two additional Atom 2.0 specific classes.
`Laminas\Feed\Writer\Source` and `Laminas\Feed\Writer\Deleted`. The former implements Atom 2.0 source
elements which carry source feed metadata for a specific entry within an aggregate feed (i.e. the
current feed is not the entry's original source). The latter implements the Atom Tombstones *RFC*
allowing feeds to carry references to entries which have been deleted.

While there are two main data container types, there are four renderers - two matching container
renderers per supported feed type. Each renderer accepts a container, and based on its content
attempts to generate valid feed markup. If the renderer is unable to generate valid feed markup,
perhaps due to the container missing an obligatory data point, it will report this by throwing an
`Exception`. While it is possible to ignore `Exception`s, this removes the default safeguard of
ensuring you have sufficient data set to render a wholly valid feed.

To explain this more clearly, you may construct a set of data containers for a feed where there is a
Feed container, into which has been added some Entry containers and a Deleted container. This forms
a data hierarchy resembling a normal feed. When rendering is performed, this hierarchy has its
pieces passed to relevant renderers and the partial feeds (all DOMDocuments) are then pieced
together to create a complete feed. In the case of Source or Deleted (Tomestone) containers, these
are rendered only for Atom 2.0 and ignored for *RSS*.

Due to the system being divided between data containers and renderers, it can make Extensions
somewhat interesting. A typical Extension offering namespaced feed and entry level elements, must
itself reflect the exact same architecture, i.e. offer feed and entry level data containers, and
matching renderers. There is, fortunately, no complex integration work required since all Extension
classes are simply registered and automatically used by the core classes. We'll meet Extensions in
more detail at the end of this section.

## Getting Started

Using `Laminas\Feed\Writer\Writer` is as simple as setting data and triggering the renderer. Here is an
example to generate a minimal Atom 1.0 feed. As this demonstrates, each feed or entry uses a
separate data container.

```php
/**
 * Create the parent feed
 */
$feed = new Laminas\Feed\Writer\Feed;
$feed->setTitle('Paddy\'s Blog');
$feed->setLink('http://www.example.com');
$feed->setFeedLink('http://www.example.com/atom', 'atom');
$feed->addAuthor(array(
    'name'  => 'Paddy',
    'email' => 'paddy@example.com',
    'uri'   => 'http://www.example.com',
));
$feed->setDateModified(time());
$feed->addHub('http://pubsubhubbub.appspot.com/');

/**
 * Add one or more entries. Note that entries must
 * be manually added once created.
 */
$entry = $feed->createEntry();
$entry->setTitle('All Your Base Are Belong To Us');
$entry->setLink('http://www.example.com/all-your-base-are-belong-to-us');
$entry->addAuthor(array(
    'name'  => 'Paddy',
    'email' => 'paddy@example.com',
    'uri'   => 'http://www.example.com',
));
$entry->setDateModified(time());
$entry->setDateCreated(time());
$entry->setDescription('Exposing the difficultly of porting games to English.');
$entry->setContent(
    'I am not writing the article. The example is long enough as is ;).'
);
$feed->addEntry($entry);

/**
 * Render the resulting feed to Atom 1.0 and assign to $out.
 * You can substitute "atom" with "rss" to generate an RSS 2.0 feed.
 */
$out = $feed->export('atom');
```

The output rendered should be as follows:

```php
<?xml version="1.0" encoding="utf-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">
    <title type="text">Paddy's Blog</title>
    <subtitle type="text">Writing about PC Games since 176 BC.</subtitle>
    <updated>2009-12-14T20:28:18+00:00</updated>
    <generator uri="https://getlaminas.org" version="1.10.0alpha">
        Laminas\Feed\Writer
    </generator>
    <link rel="alternate" type="text/html" href="http://www.example.com"/>
    <link rel="self" type="application/atom+xml"
        href="http://www.example.com/atom"/>
    <id>http://www.example.com</id>
    <author>
        <name>Paddy</name>
        <email>paddy@example.com</email>
        <uri>http://www.example.com</uri>
    </author>
    <link rel="hub" href="http://pubsubhubbub.appspot.com/"/>
    <entry>
        <title type="html"><![CDATA[All Your Base Are Belong To
            Us]]></title>
        <summary type="html">
            <![CDATA[Exposing the difficultly of porting games to
                English.]]>
        </summary>
        <published>2009-12-14T20:28:18+00:00</published>
        <updated>2009-12-14T20:28:18+00:00</updated>
        <link rel="alternate" type="text/html"
             href="http://www.example.com/all-your-base-are-belong-to-us"/>
        <id>http://www.example.com/all-your-base-are-belong-to-us</id>
        <author>
            <name>Paddy</name>
            <email>paddy@example.com</email>
            <uri>http://www.example.com</uri>
        </author>
        <content type="html">
            <![CDATA[I am not writing the article.
                     The example is long enough as is ;).]]>
        </content>
    </entry>
</feed>
```

This is a perfectly valid Atom 1.0 example. It should be noted that omitting an obligatory point of
data, such as a title, will trigger an `Exception` when rendering as Atom 1.0. This will differ for
*RSS* 2.0 since a title may be omitted so long as a description is present. This gives rise to
Exceptions that differ between the two standards depending on the renderer in use. By design,
`Laminas\Feed\Writer\Writer` will not render an invalid feed for either standard unless the end-user
deliberately elects to ignore all Exceptions. This built in safeguard was added to ensure users
without in-depth knowledge of the relevant specifications have a bit less to worry about.

## Setting Feed Data Points

Before you can render a feed, you must first setup the data necessary for the feed being rendered.
This utilises a simple setter style *API* which doubles as an initial method for validating the data
being set. By design, the *API* closely matches that for `Laminas\Feed\Reader\Reader` to avoid undue
confusion and uncertainty.

> ## Note
Users have commented that the lack of a simple array based notation for input data gives rise to
lengthy tracts of code. This will be addressed in a future release.

`Laminas\Feed\Writer\Writer` offers this *API* via its data container classes `Laminas\Feed\Writer\Feed`
and `Laminas\Feed\Writer\Entry` (not to mention the Atom 2.0 specific and Extension classes). These
classes merely store all feed data in a type-agnostic manner, meaning you may reuse any data
container with any renderer without requiring additional work. Both classes are also amenable to
Extensions, meaning that an Extension may define its own container classes which are registered to
the base container classes as extensions, and are checked when any method call triggers the base
container's `__call()` method.

Here's a summary of the Core *API* for Feeds. You should note it comprises not only the basic *RSS*
and Atom standards, but also accounts for a number of included Extensions bundled with
`Laminas\Feed\Writer\Writer`. The naming of these Extension sourced methods remain fairly generic - all
Extension methods operate at the same level as the Core *API* though we do allow you to retrieve any
specific Extension object separately if required.

The Feed Level *API* for data is contained in `Laminas\Feed\Writer\Feed`. In addition to the *API*
detailed below, the class also implements the `Countable` and `Iterator` interfaces.

> ## Note
In addition to these setters, there are also matching getters to retrieve data from the Entry data
container. For example, `setImage()` is matched with a `getImage()` method.

## Setting Entry Data Points

Here's a summary of the Core *API* for Entries and Items. You should note it comprises not only the
basic *RSS* and Atom standards, but also accounts for a number of included Extensions bundled with
`Laminas\Feed\Writer\Writer`. The naming of these Extension sourced methods remain fairly generic - all
Extension methods operate at the same level as the Core *API* though we do allow you to retrieve any
specific Extension object separately if required.

The Entry Level *API* for data is contained in `Laminas\Feed\Writer\Entry`.

> ## Note
In addition to these setters, there are also matching getters to retrieve data from the Entry data
container.
