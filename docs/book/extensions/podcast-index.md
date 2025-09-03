# Podcast Index

The Podcast Index Extension adds support for
the [Podcast Index RSS namespace](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/1.0.md),
an open source project which consolidates new features for podcasts into a
single namespace.

See the [Podcast Index website](https://podcastindex.org) for more information about the project.

## Channel API

### GET methods

| Method                                               | Description                                                                                                                                                                                                                                                                                                                     |
|------------------------------------------------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `isLocked()` `isPodcastIndexLocked()`                | Returns whether the feed is open for importing to new platforms.                                                                                                                                                                                                                                                                |
| `getLockOwner()` `getPodcastIndexLockOwner()`        | Returns the email address for owner verification.                                                                                                                                                                                                                                                                               |
| `getFunding()` `getPodcastIndexFunding()`            | Returns funding information. The output is an object with `url` and `title` (node value) properties.                                                                                                                                                                                                                            |
| `getPodcastIndexLicense()`                           | Returns license information. The output is an object with `identifier` (node value) and `url` properties.                                                                                                                                                                                                                       |
| `getPodcastIndexLocation()`                          | Returns location information. The output is an object with `description` (node value), `geo` and `osm` properties.                                                                                                                                                                                                              |
| `getPodcastIndexImages()`                            | Returns information on responsive images. The output is an object with a `srcset` property.                                                                                                                                                                                                                                     |
| `getPodcastIndexUpdateFrequency()`                   | Returns information on the intended release schedule. The output is an object with `description` (node value), `complete`, `dtstart` and `rrule` properties.                                                                                                                                                                    |
| `getPodcastIndexPeople()` `getPodcastIndexPersons()` | Returns information on the involved people. The output is an array of objects, each with the properties `name` (node value), `role`, `group`, `img` and `href`.                                                                                                                                                                 |
| `getPodcastIndexTrailer()`                           | Returns information on the podcast trailer. The output is an object with the properties `title` (node value), `pubdate`, `url`, `length`, `type` and `season`.                                                                                                                                                                  |
| `getPodcastIndexGuid()`                              | Returns the podcast guid. The output is an object with the property `value`.                                                                                                                                                                                                                                                    |
| `getPodcastIndexMedium()`                            | Returns the podcast medium. The output is an object with the property `value`.                                                                                                                                                                                                                                                  |
| `getPodcastIndexBlocks()`                            | Returns whether or which platforms are allowed to publicly display this feed. The output is an object with the properties `value` and `id`.                                                                                                                                                                                     |
| `getPodcastIndexTxts()`                              | Returns information on topics that do not have their own tags. The output is an object with the properties `value` and `purpose`.                                                                                                                                                                                               |
| `getPodcastIndexPodping()`                           | Returns whether the feed sends out podping notifications when changes are made to it. The output is an object with the property `usesPodping`.                                                                                                                                                                                  |
| `getPodcastIndexRemoteItems()`                       | Returns the remote items assigned as direct children of the feed. The output is an array of objects with each the properties `feedGuid`, `feedUrl`, `itemGuid`, `medium` and `title`. Note: Nested remote items that belong to other namespaces need to be managed with the methods of those namespaces.                        |
| `getPodcastIndexPodroll()`                           | Returns the remote items assigned to the podroll tag of the feed. The output is an array of objects with each the properties `feedGuid`, `feedUrl`, `itemGuid`, `medium` and `title`.                                                                                                                                           |
| `getPodcastIndexPublisher()`                         | Returns one remote item assigned to the publisher tag of the feed. The output is an object with the properties `feedGuid`, `feedUrl`, `itemGuid`, `medium` and `title`.                                                                                                                                                         |
| `getPodcastIndexValues()`                            | Returns the values and their valueRecipients. The output is an array of objects, each with the properties `type`, `method`, `suggested` and `valueRecipients`. `valueRecipients` is an array of objects itself, each object containing the attributes `name`, `type`, `address`, `split`, `customKey`, `customValue` and `fee`. |
| `getPodcastIndexSocialInteracts()`                   | Returns the social interacts. The output is an array of objects, each with the properties `protocol`, `uri`, `priority`, `accountId` and `accountUrl`.                                                                                                                                                                          |

### SET methods

| Method                                               | Description                                                                                                                                                                                                                                                                                                                                                                                                                                      |
|------------------------------------------------------|--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `setPodcastIndexLocked()`                            | Expects an array with the required keys `value` and `owner`.                                                                                                                                                                                                                                                                                                                                                                                     |
| `setPodcastIndexFunding()`                           | Expects an array with the required keys `title` (node value) and `url`.                                                                                                                                                                                                                                                                                                                                                                          |
| `setPodcastIndexLicense()`                           | Expects an array with the required keys `identifier` (node value) and `url`.                                                                                                                                                                                                                                                                                                                                                                     |
| `setPodcastIndexLocation()`                          | Expects an array with the required key `description` (node value) and the optional keys `geo` and `osm`.                                                                                                                                                                                                                                                                                                                                         |
| `setPodcastIndexImages()`                            | Expects an array with the required key `srcset`.                                                                                                                                                                                                                                                                                                                                                                                                 |
| `setPodcastIndexUpdateFrequency()`                   | Expects an array with the required key `description` (node value) and the optional keys `complete` (bool), `dtstart` (ISO8601 string) and `rrule`.                                                                                                                                                                                                                                                                                               |
| `addPodcastIndexPerson()`                            | Expects an array with the required key `name` (node value) and the optional keys `role`, `group`, `img` and `href`.                                                                                                                                                                                                                                                                                                                              |
| `setPodcastIndexPeople()` `setPodcastIndexPersons()` | Expects an array of arrays with each the required key `name` (node value) and the optional keys `role`, `group`, `img` and `href`.                                                                                                                                                                                                                                                                                                               |
| `setPodcastIndexTrailer()`                           | Expects an array with the required keys `title` (node value), `pubdate` and `url` and the optional keys `length`, `type` and `season`.                                                                                                                                                                                                                                                                                                           |
| `setPodcastIndexGuid()`                              | Expects an array with the required key `value`.                                                                                                                                                                                                                                                                                                                                                                                                  |
| `setPodcastIndexMedium()`                            | Expects an array with the required key `value`.                                                                                                                                                                                                                                                                                                                                                                                                  |
| `addPodcastIndexBlock()`                             | Expects an array with the required key `value` and the optional key `id`.                                                                                                                                                                                                                                                                                                                                                                        |
| `setPodcastIndexBlocks()`                            | Expects an array of arrays with each the required key `value` and the optional key `id`.                                                                                                                                                                                                                                                                                                                                                         |
| `addPodcastIndexTxt()`                               | Expects an array with the required key `value` and the optional key `purpose`.                                                                                                                                                                                                                                                                                                                                                                   |
| `setPodcastIndexTxts()`                              | Expects an array of arrays with each the required key `value` and the optional key `purpose`.                                                                                                                                                                                                                                                                                                                                                    |
| `setPodcastIndexPodping()`                           | Expects an array with the required key `usesPodping` (node value).                                                                                                                                                                                                                                                                                                                                                                               |
| `addPodcastIndexRemoteItem()`                        | Expects an array with the required key `feedGuid` and the optional keys `feedUrl`, `itemGuid`, `medium` and `title`.                                                                                                                                                                                                                                                                                                                             |
| `setPodcastIndexRemoteItems()`                       | Expects an array of arrays with each the required key `feedGuid` and the optional keys `feedUrl`, `itemGuid`, `medium` and `title`. _Note: Nested remote items that belong to other namespaces need to be managed with the methods of those namespaces_.                                                                                                                                                                                         |
| `setPodcastIndexPodroll()`                           | Expects an array of remote items arrays with each the required key `feedGuid` and the optional keys `feedUrl`, `itemGuid`, `medium` and `title`.                                                                                                                                                                                                                                                                                                 |
| `addPodcastIndexPodrollRemoteItem()`                 | Expects an array with the required key `feedGuid` and the optional keys `feedUrl`, `itemGuid`, `medium` and `title`.                                                                                                                                                                                                                                                                                                                             |
| `setPodcastIndexPublisher()`                         | Expects an array with the required key `feedGuid` and the optional keys `feedUrl`, `itemGuid`, `medium` and `title`.                                                                                                                                                                                                                                                                                                                             |
| `addPodcastIndexValue()`                             | Expects two arguments: First, an array of the value attributes, containing the required keys `type` and `method` and the optional key `suggested`. As second argument, and array of one or more valueRecipient entries is expected. Each entry must be of type array, containing the required keys `type`, `address` and `split` and the optional keys `name`, `customKey`, `customValue` and `fee`. You may consider the example further below. |
| `resetPodcastIndexValues()`                          | Expects no arguments. Removes existing value entries.                                                                                                                                                                                                                                                                                                                                                                                            |
| `setPodcastIndexSocialInteracts()`                   | Expects an array of arrays, each with the required keys `protocol` and `uri`, and the optional keys `priority`, `accountId` and `accountUrl`.                                                                                                                                                                                                                                                                                                    |
| `addPodcastIndexSocialInteract()`                    | Expects an array with the required keys `protocol` and `uri`, and the optional keys `priority`, `accountId` and `accountUrl`.                                                                                                                                                                                                                                                                                                                    |

## Episode API

### GET methods

| Method                                               | Description                                                                                                                                                                                                                                                                                                                     |
|------------------------------------------------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `getTranscript()` `getPodcastIndexTranscript()`      | Returns transcript information for the entry. The output is an object with `url`, `type`, `language` and `rel` properties.                                                                                                                                                                                                      |
| `getChapters()` `getPodcastIndexChapters()`          | Returns chapter information for the entry. The output is an object with `url` and `type` properties.                                                                                                                                                                                                                            |
| `getSoundbites()` `getPodcastIndexSoundbites()`      | Returns soundbites for the entry. The output is an array of objects with `title` (node value), `startTime` and `duration` properties.                                                                                                                                                                                           |
| `getPodcastIndexLocation()`                          | Returns location information. The output is an object with `description` (node value), `rel`, `country`, geo` and `osm` properties.                                                                                                                                                                                             |
| `getPodcastIndexLicense()`                           | Returns license information. The output is an object with `identifier` (node value) and `url` properties.                                                                                                                                                                                                                       |
| `getPodcastIndexPeople()` `getPodcastIndexPersons()` | Returns information on the involved people. The output is an array of objects, each with the properties `name` (node value), `role`, `group`, `img` and `href`.                                                                                                                                                                 |
| `getPodcastIndexTxts()`                              | Returns information on topics that do not have their own tags. The output is an object with the properties `value` and `purpose`.                                                                                                                                                                                               |
| `getPodcastIndexSocialInteracts()`                   | Returns the social interacts. The output is an array of objects, each with the properties `protocol`, `uri`, `priority`, `accountId` and `accountUrl`.                                                                                                                                                                          |
| `getPodcastIndexValues()`                            | Returns the values and their valueRecipients. The output is an array of objects, each with the properties `type`, `method`, `suggested` and `valueRecipients`. `valueRecipients` is an array of objects itself, each object containing the attributes `name`, `type`, `address`, `split`, `customKey`, `customValue` and `fee`. |
| `getPodcastIndexSeason()`                            | Returns season information. The output is an object with the properties `value` and `name`.                                                                                                                                                                                                                                     |

### SET methods

| Method                                               | Description                                                                                                                                                                                                                                                                                                                                                                                   |
|------------------------------------------------------|-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `setPodcastIndexTranscript()`                        | Expects an array with the required keys `url` and `type`, and with the optional keys `language` and `rel`.                                                                                                                                                                                                                                                                                    |
| `setPodcastIndexChapters()`                          | Expects an array with the required keys `url` and `type`.                                                                                                                                                                                                                                                                                                                                     |
| `addPodcastIndexSoundbite()`                         | Expects an array with the required keys `duration` and `startTime`, and with the optional key `title` (node value).                                                                                                                                                                                                                                                                           |
| `addPodcastIndexSoundbites()`                        | Expects an array of soundbite entries, each itself an array with the required keys `duration` and `startTime`, and with the optional key `title`.                                                                                                                                                                                                                                             |
| `setPodcastIndexSoundbites()`                        | Same as above, but replaces all existing entries.                                                                                                                                                                                                                                                                                                                                             |
| `setPodcastIndexLocation()`                          | Expects an array with the required key `description` (node value) and the optional keys `rel`, `country`, `geo` and `osm`.                                                                                                                                                                                                                                                                    |
| `setPodcastIndexLicense()`                           | Expects an array with the required keys `identifier` (node value) and `url`.                                                                                                                                                                                                                                                                                                                  |
| `addPodcastIndexPerson()`                            | Expects an array with the required key `name` (node value) and the optional keys `role`, `group`, `img` and `href`.                                                                                                                                                                                                                                                                           |
| `setPodcastIndexPeople()` `setPodcastIndexPersons()` | Expects an array of arrays with each the required key `name` (node value) and the optional keys `role`, `group`, `img` and `href`.                                                                                                                                                                                                                                                            |
| `addPodcastIndexTxt()`                               | Expects an array with the required key `value` and the optional key `purpose`.                                                                                                                                                                                                                                                                                                                |
| `setPodcastIndexTxts()`                              | Expects an array of arrays with each the required key `value` and the optional key `purpose`.                                                                                                                                                                                                                                                                                                 |
| `addPodcastIndexSocialInteract()`                    | Expects an array with the required keys `protocol` and `uri`, and the optional keys `priority`, `accountId` and `accountUrl`.                                                                                                                                                                                                                                                                 |
| `setPodcastIndexSocialInteracts()`                   | Expects an array of arrays, each with the required keys `protocol` and `uri`, and the optional keys `priority`, `accountId` and `accountUrl`.                                                                                                                                                                                                                                                 |
| `addPodcastIndexValue()`                             | _This method is quite complex, you may consider the examples further below and the official PodcastIndex documentation for the detailed structure of the arguments to pass_. Expects three arguments: First, an array of the `value` attributes. Second, an array of one or more `valueRecipient` entries. The third argument is optional: An array of one or more `valueTimeSplit` elements. |
| `resetPodcastIndexValues()`                          | Expects no arguments. Removes existing value entries.                                                                                                                                                                                                                                                                                                                                         |
| `setPodcastIndexSeason()`                            | Expects an array with the required key `value` and the optional key `name`.                                                                                                                                                                                                                                                                                                                   |

## Examples for using the `value` namespace

Please also consider the official PodcastIndex documentation on the
[value](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/tags/value.md),
[valueRecipient](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/tags/value-recipient.md)
and [valueTimeSplit](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/tags/value-time-split.md) namespaces.

### Channel: `addPodcastIndexValue()`

Usage:

```php
$value = [
    'type'      => "lightning",
    'method'    => "keysend",
    'suggested' => 0.00000005000,
];
$valueRecipients = [
    [
        'name'    => "Alice (Podcaster)",
        'type'    => "node",
        'address' => "02d5c1bf8b940dc9cadca86d1b0a3c37fbe39cee4c7e839e33bef9174531d27f52",
        'split'   => 40,
    ],
    [
        'name'    => "Bob (Podcaster)",
        'type'    => "node",
        'address' => "032f4ffbbafffbe51726ad3c164a3d0d37ec27bc67b29a159b0f49ae8ac21b8508",
        'split'   => 60,
    ],
];
$feedWriter->addPodcastIndexValue($value, $valueRecipients);
```

Rendered value tag:

```xml
<podcast:value type="lightning" method="keysend" suggested="0.00000005000">
    <podcast:valueRecipient name="Alice (Podcaster)" type="node" address="02d5c1bf8b940dc9cadca86d1b0a3c37fbe39cee4c7e839e33bef9174531d27f52" split="40"/>
    <podcast:valueRecipient name="Bob (Podcaster)" type="node" address="032f4ffbbafffbe51726ad3c164a3d0d37ec27bc67b29a159b0f49ae8ac21b8508" split="60"/>
</podcast:value>
```

### Episode: `addPodcastIndexValue()` with `valueTimeSplit` and nested `remoteItem`

Usage:

```php
$value      = [
    'type'      => "lightning",
    'method'    => "keysend",
    'suggested' => 0.00000005000,
];
$valueRecipients = [
    [
        'name'    => "Alice (Podcaster)",
        'type'    => "node",
        'address' => "02d5c1bf8b940dc9cadca86d1b0a3c37fbe39cee4c7e839e33bef9174531d27f52",
        'split'   => 40,
    ],
    [
        'name'    => "Bob (Podcaster)",
        'type'    => "node",
        'address' => "032f4ffbbafffbe51726ad3c164a3d0d37ec27bc67b29a159b0f49ae8ac21b8508",
        'split'   => 60,
    ],
];
$valueTimeSplits = [
    [
        'startTime'  => 60,
        'duration'   => 237,
        'remotePercentage' => 95,
        'remoteItem' => [
            'itemGuid' => "https://podcastindex.org/podcast/4148683#1",
            'feedGuid' => "a94f5cc9-8c58-55fc-91fe-a324087a655b",
            'medium'   => "music",
        ],
    ],
    [
        'startTime'  => 330,
        'duration'   => 53,
        'remotePercentage' => 95,
        'remoteStartTime' => 174,
        'remoteItem' => [
            'itemGuid' => "https://podcastindex.org/podcast/4148683#3",
            'feedGuid' => "b83f5cc9-8c58-55fc-91fe-a324087a644c",
            'medium'   => "music",
        ],
    ],
];
$entryWriter->addPodcastIndexValue($value, $newRecipients, $valueRecipients);
```

Rendered value tag:

```xml
<podcast:value type="lightning" method="keysend" suggested="0.00000005000">
    <podcast:valueRecipient name="Alice (Podcaster)" type="node" address="02d5c1bf8b940dc9cadca86d1b0a3c37fbe39cee4c7e839e33bef9174531d27f52" split="40"/>
    <podcast:valueRecipient name="Bob (Podcaster)" type="node" address="032f4ffbbafffbe51726ad3c164a3d0d37ec27bc67b29a159b0f49ae8ac21b8508" split="60"/>
    <podcast:valueTimeSplit startTime="60" duration="237" remotePercentage="95">
        <podcast:remoteItem itemGuid="https://podcastindex.org/podcast/4148683#1" feedGuid="a94f5cc9-8c58-55fc-91fe-a324087a655b" medium="music" />
    </podcast:valueTimeSplit>
    <podcast:valueTimeSplit startTime="330" duration="53" remoteStartTime="174" remotePercentage="95">
        <podcast:remoteItem itemGuid="https://podcastindex.org/podcast/4148683#3" feedGuid="b83f5cc9-8c58-55fc-91fe-a324087a644c" medium="music" />
    </podcast:valueTimeSplit>
</podcast:value>
```

### Episode: `addPodcastIndexValue()` with `valueTimeSplit` and nested `valueRecipient`

Usage:

```php
$value      = [
    'type'      => "lightning",
    'method'    => "keysend",
    'suggested' => 0.00000005000,
];
$valueRecipients = [
    [
        'name'    => "Alice (Podcaster)",
        'type'    => "node",
        'address' => "02d5c1bf8b940dc9cadca86d1b0a3c37fbe39cee4c7e839e33bef9174531d27f52",
        'split'   => 40,
    ],
    [
        'name'    => "Bob (Podcaster)",
        'type'    => "node",
        'address' => "032f4ffbbafffbe51726ad3c164a3d0d37ec27bc67b29a159b0f49ae8ac21b8508",
        'split'   => 60,
    ],
];
$valueTimeSplits = [
    [
        'startTime'       => 63,
        'duration'        => 388,
        'valueRecipients' => [
            [
                'name'        => "Alice (Podcaster)",
                'type'        => "node",
                'address'     => "02d5c1bf8b940dc9cadca86d1b0a3c37fbe39cee4c7e839e33bef9174531d27f52",
                'split'       => 80,
            ],
            [
                 'name'    => "Malcolm (Guest)",
                 'type'    => "node",
                 'address' => "02dd306e68c46681aa21d88a436fb35355a8579dd30201581cefa17cb179fc4c15",
                 'split'   => 20,
            ],
        ],
    ],
];
$entryWriter->addPodcastIndexValue($value, $newRecipients, $valueRecipients);
```

Rendered value tag:

```xml
<podcast:value type="lightning" method="keysend" suggested="0.00000005000">
    <podcast:valueRecipient name="Alice (Podcaster)" type="node" address="02d5c1bf8b940dc9cadca86d1b0a3c37fbe39cee4c7e839e33bef9174531d27f52" split="40"/>
    <podcast:valueRecipient name="Bob (Podcaster)" type="node" address="032f4ffbbafffbe51726ad3c164a3d0d37ec27bc67b29a159b0f49ae8ac21b8508" split="60"/>
    <podcast:valueTimeSplit startTime="63" duration="388">
        <podcast:valueRecipient name="Alice (Podcaster)" type="node" address="02d5c1bf8b940dc9cadca86d1b0a3c37fbe39cee4c7e839e33bef9174531d27f52" split="80" />
        <podcast:valueRecipient name="Malcolm (Guest)" type="node" address="02dd306e68c46681aa21d88a436fb35355a8579dd30201581cefa17cb179fc4c15" split="20" />
    </podcast:valueTimeSplit>
</podcast:value>
```
