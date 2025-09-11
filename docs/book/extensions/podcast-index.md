# Podcast Index

The Podcast Index Extension adds support for
the [Podcast Index RSS namespace](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/1.0.md),
an open source project which consolidates new features for podcasts into a
single namespace.

See the [Podcast Index website](https://podcastindex.org) for more information about the project.

## Channel API

### GET methods

| Method                                               | Description                                                                                                                                                                                                                                                                                                              |
|------------------------------------------------------|--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `getPodcastIndexBlocks()`                            | Returns whether or which platforms are allowed to publicly display this feed. The output is an object with the properties `value` and `id`.                                                                                                                                                                              |
| `isLocked()` `isPodcastIndexLocked()`                | Returns whether the feed is open for importing to new platforms.                                                                                                                                                                                                                                                         |
| `getLockOwner()` `getPodcastIndexLockOwner()`        | Returns the email address for owner verification.                                                                                                                                                                                                                                                                        |
| `getFunding()` `getPodcastIndexFunding()`            | Returns funding information. The output is an object with `url` and `title` properties.                                                                                                                                                                                                                                  |
| `getPodcastIndexLicense()`                           | Returns license information. The output is an object with `identifier` and `url` properties.                                                                                                                                                                                                                             |
| `getPodcastIndexLocation()`                          | Returns location information. The output is an object with `description`, `geo`, `osm`, `rel` and `country` properties.                                                                                                                                                                                                  |
| `getPodcastIndexImages()`                            | _Deprecated:_ Returns information on responsive images. The output is an object with a `srcset` property.                                                                                                                                                                                                                |
| `getPodcastIndexDetailedImages()`                    | Returns the images. The output is an array of objects, each with the properties `href`, `alt`, `aspectRatio`, `width`, `height`, `type` and `purpose`.                                                                                                                                                                   |
| `getPodcastIndexUpdateFrequency()`                   | Returns information on the intended release schedule. The output is an object with `description`, `complete`, `dtstart` and `rrule` properties.                                                                                                                                                                          |
| `getPodcastIndexPeople()` `getPodcastIndexPersons()` | Returns information on the involved people. The output is an array of objects, each with the properties `name`, `role`, `group`, `img` and `href`.                                                                                                                                                                       |
| `getPodcastIndexTrailer()`                           | Returns information on the podcast trailer. The output is an object with the properties `title` , `pubdate`, `url`, `length`, `type` and `season`.                                                                                                                                                                       |
| `getPodcastIndexGuid()`                              | Returns the podcast guid. The output is an object with the property `value`.                                                                                                                                                                                                                                             |
| `getPodcastIndexMedium()`                            | Returns the podcast medium. The output is an object with the property `value`.                                                                                                                                                                                                                                           |
| `getPodcastIndexTxts()`                              | Returns information on topics that do not have their own tags. The output is an object with the properties `value` and `purpose`.                                                                                                                                                                                        |
| `getPodcastIndexPodping()`                           | Returns whether the feed sends out podping notifications when changes are made to it. The output is an object with the property `usesPodping`.                                                                                                                                                                           |
| `getPodcastIndexRemoteItems()`                       | Returns the remote items assigned as direct children of the feed. The output is an array of objects with each the properties `feedGuid`, `feedUrl`, `itemGuid`, `medium` and `title`. Note: Nested remote items that belong to other namespaces need to be managed with the methods of those namespaces.                 |
| `getPodcastIndexPodroll()`                           | Returns the remote items assigned to the podroll tag of the feed. The output is an array of objects with each the properties `feedGuid`, `feedUrl`, `itemGuid`, `medium` and `title`.                                                                                                                                    |
| `getPodcastIndexPublisher()`                         | Returns one remote item assigned to the publisher tag of the feed. The output is an object with the properties `feedGuid`, `feedUrl`, `itemGuid`, `medium` and `title`.                                                                                                                                                  |
| `getPodcastIndexValues()`                            | Returns the values and their valueRecipients. The output is an array of objects, each with the properties `type`, `method`, `suggested` and `valueRecipients`. `valueRecipients` is an array of objects itself, each containing the attributes `name`, `type`, `address`, `split`, `customKey`, `customValue` and `fee`. |
| `getPodcastIndexSocialInteracts()`                   | Returns the social interacts. The output is an array of objects, each with the properties `protocol`, `uri`, `priority`, `accountId` and `accountUrl`.                                                                                                                                                                   |

### SET methods

| Method                                               | Description                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 |
|------------------------------------------------------|-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `setPodcastIndexLocked()`                            | Sets the tag [podcast:locked](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/tags/locked.md). Expects an array with the required keys `value` (node value) and `owner`.                                                                                                                                                                                                                                                                                                                                                                               |
| `setPodcastIndexFunding()`                           | Sets the tag [podcast:funding](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/tags/funding.md). Expects an array with the required keys `title` (node value) and `url`.                                                                                                                                                                                                                                                                                                                                                                               |
| `setPodcastIndexLicense()`                           | Sets the tag [podcast:license](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/tags/license.md). Expects an array with the required keys `identifier` (node value) and the optional key `url`.                                                                                                                                                                                                                                                                                                                                                         |
| `setPodcastIndexLocation()`                          | Sets the tag [podcast:location](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/tags/location.md). Expects an array with the required key `description` (node value) and the optional keys `geo`, `osm`, `rel` and `country`.                                                                                                                                                                                                                                                                                                                          |
| `setPodcastIndexImages()`                            | _Deprecated:_ Sets the tag [podcast:images](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/tags/images-(deprecated).md). Expects an array with the required key `srcset`.                                                                                                                                                                                                                                                                                                                                                                             |
| `addPodcastIndexDetailedImage()`                     | Adds a [podcast:image](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/tags/image.md) tag. Expects an array with the required key `href` and the optional keys `alt`, `aspectRatio`, `width`, `height`, `type` and `purpose`.                                                                                                                                                                                                                                                                                                                          |
| `setPodcastIndexDetailedImages()`                    | Sets multiple [podcast:image](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/tags/image.md) tags. Expects an array of arrays, each with the keys as defined in `addPodcastIndexDetailedImage()`.                                                                                                                                                                                                                                                                                                                                                      |
| `setPodcastIndexUpdateFrequency()`                   | Sets the tag [podcast:updateFrequency](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/tags/update-frequency.md). Expects an array with the required key `description` (node value) and the optional keys `complete`, `dtstart` and `rrule`.                                                                                                                                                                                                                                                                                                           |
| `addPodcastIndexPerson()`                            | Adds a [podcast:person](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/tags/person.md) tag. Expects an array with the required key `name` (node value) and the optional keys `role`, `group`, `img` and `href`.                                                                                                                                                                                                                                                                                                                                       |
| `setPodcastIndexPeople()` `setPodcastIndexPersons()` | Sets multiple [podcast:person](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/tags/person.md) tags. Expects an array of arrays, each with the keys as defined in `setPodcastIndexPeople()`.                                                                                                                                                                                                                                                                                                                                                           |
| `setPodcastIndexTrailer()`                           | Sets the tag [podcast:trailer](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/tags/trailer.md). Expects an array with the required keys `title` (node value), `pubdate` and `url` and the optional keys `length`, `type` and `season`.                                                                                                                                                                                                                                                                                                                |
| `setPodcastIndexGuid()`                              | Sets the tag [podcast:guid](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/tags/guid.md). Expects an array with the required key `value` (node value).                                                                                                                                                                                                                                                                                                                                                                                                |
| `setPodcastIndexMedium()`                            | Sets the tag [podcast:medium](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/tags/medium.md). Expects an array with the required key `value` (node value).                                                                                                                                                                                                                                                                                                                                                                                            |
| `addPodcastIndexBlock()`                             | Adds a [podcast:block](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/tags/block.md) tag. Expects an array with the required key `value` (node value) and the optional key `id`.                                                                                                                                                                                                                                                                                                                                                                      |
| `setPodcastIndexBlocks()`                            | Sets multiple [podcast:block](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/tags/block.md) tags. Expects an array of arrays, each with the keys as defined in `addPodcastIndexBlock()`.                                                                                                                                                                                                                                                                                                                                                              |
| `addPodcastIndexTxt()`                               | Adds a [podcast:txt](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/tags/txt.md) tag. Expects an array with the required key `value` (node value) and the optional key `purpose`.                                                                                                                                                                                                                                                                                                                                                                     |
| `setPodcastIndexTxts()`                              | Sets multiple [podcast:txt](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/tags/txt.md) tags. Expects an array of arrays, each with the keys as defined in `addPodcastIndexTxt()`.                                                                                                                                                                                                                                                                                                                                                                    |
| `setPodcastIndexPodping()`                           | Sets the tag [podcast:podping](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/tags/podping.md). Expects an array with the required key `usesPodping`.                                                                                                                                                                                                                                                                                                                                                                                                 |
| `addPodcastIndexRemoteItem()`                        | Adds a  [podcast:remoteItem](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/tags/remote-item.md) tag. Expects an array with the required key `feedGuid` and the optional keys `feedUrl`, `itemGuid`, `medium` and `title`.                                                                                                                                                                                                                                                                                                                            |
| `setPodcastIndexRemoteItems()`                       | Sets multiple [podcast:remoteItem](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/tags/remote-item.md) tags. Expects an array of arrays, each with the remote item attributes as defined in `addPodcastIndexRemoteItem()`. _Note: Nested remote items that belong to other namespaces need to be managed with the methods of those namespaces_.                                                                                                                                                                                                       |
| `setPodcastIndexPodroll()`                           | Sets the tag [podcast:podroll](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/tags/podroll.md). Expects an array of arrays, each with the remote item attributes as defined in `addPodcastIndexRemoteItem()`.                                                                                                                                                                                                                                                                                                                                         |
| `addPodcastIndexPodrollRemoteItem()`                 | Adds a [podcast:remoteItem](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/tags/remote-item.md) tag to an existing `podcast:podroll` element. Expects an array with the remote item attributes as defined in `addPodcastIndexRemoteItem()`.                                                                                                                                                                                                                                                                                                           |
| `setPodcastIndexPublisher()`                         | Sets the tag [podcast:publisher](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/tags/publisher.md). Expects an array with the remote item attributes as defined in `addPodcastIndexRemoteItem()`.                                                                                                                                                                                                                                                                                                                                                     |
| `addPodcastIndexValue()`                             | Adds a [podcast:value](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/tags/value.md) tag. Expects two arguments: First, an array of the value attributes, containing the required keys `type` and `method` and the optional key `suggested`. As second argument, an array of one or more valueRecipient entries is expected. Each entry must be of type array, containing the required keys `type`, `address` and `split` and the optional keys `name`, `customKey`, `customValue` and `fee`. _You may consider the [example below](#channel-value)._ |
| `resetPodcastIndexValues()`                          | Expects no arguments. Removes existing value entries.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       |
| `addPodcastIndexSocialInteract()`                    | Adds a [podcast:socialInteract](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/tags/social-interact.md) tag. Expects an array with the required keys `protocol` and `uri`, and the optional keys `priority`, `accountId` and `accountUrl`.                                                                                                                                                                                                                                                                                                            |
| `setPodcastIndexSocialInteracts()`                   | Sets multiple [podcast:socialInteract](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/tags/social-interact.md) tags. Expects an array of arrays, each with the keys as defined in `addPodcastIndexSocialInteract()`.                                                                                                                                                                                                                                                                                                                                  |

## Episode API

### GET methods

| Method                                               | Description                                                                                                                                                                                                                                                                                                                                                          |
|------------------------------------------------------|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `getPodcastIndexAlternateEnclosures()`               | Returns the alternate enclosures. The output is an array of objects, each with the properties `type`, `length`, `bitrate`, `height`, `lang`, `title`, `rel`, `codecs`, `default`, as well as the nested objects `sources` and, if existing, `integrity`. _Note: This is a complex namespace, so please consider the [examples below](#episode-alternate-enclosure)._ |
| `getTranscript()` `getPodcastIndexTranscript()`      | Returns transcript information for the entry. The output is an object with `url`, `type`, `language` and `rel` properties.                                                                                                                                                                                                                                           |
| `getChapters()` `getPodcastIndexChapters()`          | Returns chapter information for the entry. The output is an object with `url` and `type` properties.                                                                                                                                                                                                                                                                 |
| `getSoundbites()` `getPodcastIndexSoundbites()`      | Returns soundbites for the entry. The output is an array of objects with `title`, `startTime` and `duration` properties.                                                                                                                                                                                                                                             |
| `getPodcastIndexLocation()`                          | Returns location information. The output is an object with `description`, `rel`, `country`, geo` and `osm` properties.                                                                                                                                                                                                                                               |
| `getPodcastIndexLicense()`                           | Returns license information. The output is an object with `identifier` and `url` properties.                                                                                                                                                                                                                                                                         |
| `getPodcastIndexPeople()` `getPodcastIndexPersons()` | Returns information on the involved people. The output is an array of objects, each with the properties `name`, `role`, `group`, `img` and `href`.                                                                                                                                                                                                                   |
| `getPodcastIndexTxts()`                              | Returns information on topics that do not have their own tags. The output is an object with the properties `value` and `purpose`.                                                                                                                                                                                                                                    |
| `getPodcastIndexSocialInteracts()`                   | Returns the social interacts. The output is an array of objects, each with the properties `protocol`, `uri`, `priority`, `accountId` and `accountUrl`.                                                                                                                                                                                                               |
| `getPodcastIndexValues()`                            | Returns the values with nested `valueRecipients` and, if existing, `valueTimeSplits`. The output is an array of objects, each with the properties `type`, `method` and `suggested`, and the nested objects `valueRecipients` and `valueTimeSplits`. _Note: This is a complex namespace, so please consider the [examples below](#episode-value)._                    |
| `getPodcastIndexSeason()`                            | Returns season information. The output is an object with the properties `value` and `name`.                                                                                                                                                                                                                                                                          |
| `getPodcastIndexEpisode()`                           | Returns episode information. The output is an object with the properties `value` and `display`.                                                                                                                                                                                                                                                                      |
| `getPodcastIndexDetailedImages()`                    | Returns the images. The output is an array of objects, each with the properties `href`, `alt`, `aspectRatio`, `width`, `height`, `type` and `purpose`.                                                                                                                                                                                                               |

### SET methods

| Method                                               | Description                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     |
|------------------------------------------------------|-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `addPodcastIndexAlternateEnclosure()`                | Adds a [podcast:alternateEnclosure](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/tags/alternate-enclosure.md) tag.  Expects three arguments: First, an array with the `alternateEnclosure` attributes. Second, an array of one or more `source` entries. The third argument is optional: An array of `integrity` attributes.  _Note: This method is quite complex, so please consider the [examples below](#episode-alternate-enclosure)._                                              |
| `resetPodcastIndexAlternateEnclosures()`             | Expects no arguments. Removes existing alternate enclosure entries.                                                                                                                                                                                                                                                                                                                                                                                                                                             |
| `setPodcastIndexTranscript()`                        | Sets the tag [podcast:transcript](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/tags/transcript.md). Expects an array with the required keys `url` and `type`, and with the optional keys `language` and `rel`.                                                                                                                                                                                                                                                                          |
| `setPodcastIndexChapters()`                          | Sets the tag [podcast:chapters](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/tags/chapters.md).  Expects an array with the required keys `url` and `type`.                                                                                                                                                                                                                                                                                                                              |
| `addPodcastIndexSoundbite()`                         | Adds a single [podcast:soundbite](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/tags/soundbite.md) tag.  Expects an array with the required keys `duration` and `startTime`, and with the optional key `title` (node value).                                                                                                                                                                                                                                                             |
| `addPodcastIndexSoundbites()`                        | Adds multiple [podcast:soundbite](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/tags/soundbite.md) tags. Expects an array of arrays, each with the keys as defined in `addPodcastIndexSoundbite()`.                                                                                                                                                                                                                                                                                      |
| `setPodcastIndexSoundbites()`                        | Sets multiple [podcast:soundbite](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/tags/soundbite.md) tags. Same as `addPodcastIndexSoundbites()`, but replaces existing soundbite entries.                                                                                                                                                                                                                                                                                                 |
| `setPodcastIndexLocation()`                          | Sets the tag [podcast:location](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/tags/location.md).  Expects an array with the required key `description` (node value) and the optional keys `rel`, `country`, `geo` and `osm`.                                                                                                                                                                                                                                                             |
| `setPodcastIndexLicense()`                           | Sets the tag [podcast:license](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/tags/license.md).  Expects an array with the required key `identifier` (node value) and the optional key `url`.                                                                                                                                                                                                                                                                                             |
| `addPodcastIndexPerson()`                            | Adds a [podcast:person](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/tags/person.md) tag.  Expects an array with the required key `name` (node value) and the optional keys `role`, `group`, `img` and `href`.                                                                                                                                                                                                                                                                          |
| `setPodcastIndexPeople()` `setPodcastIndexPersons()` | Sets multiple [podcast:person](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/tags/person.md) tags.  Expects an array of arrays, each with the keys as defined in `addPodcastIndexPerson()`.                                                                                                                                                                                                                                                                                              |
| `addPodcastIndexTxt()`                               | Adds a [podcast:txt](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/tags/txt.md) tag.  Expects an array with the required key `value` and the optional key `purpose`.                                                                                                                                                                                                                                                                                                                     |
| `setPodcastIndexTxts()`                              | Sets multiple [podcast:txt](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/tags/txt.md) tags.  Expects an array of arrays, each with the keys as defined in `addPodcastIndexTxt()`.                                                                                                                                                                                                                                                                                                       |
| `addPodcastIndexSocialInteract()`                    | Adds a [podcast:socialInteract](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/tags/social-interact.md) tag.  Expects an array with the required keys `protocol` and `uri`, and the optional keys `priority`, `accountId` and `accountUrl`.                                                                                                                                                                                                                                               |
| `setPodcastIndexSocialInteracts()`                   | Sets multiple [podcast:socialInteract](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/tags/social-interact.md) tags.  Expects an array of arrays, each with the keys as defined in `addPodcastIndexSocialInteract()`.                                                                                                                                                                                                                                                                     |
| `addPodcastIndexValue()`                             | Adds a [podcast:value](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/tags/value.md) tag.  Expects three arguments: First, an array of the `value` attributes. Second, an array of one or more `valueRecipient` entries. The third argument is optional: An array of one or more `valueTimeSplit` elements. _Note: This method is quite complex, so please consider the [examples below](#episode-value), as well as the official PodcastIndex documentation for the detailed structure._ |
| `resetPodcastIndexValues()`                          | Expects no arguments. Removes existing value entries.                                                                                                                                                                                                                                                                                                                                                                                                                                                           |
| `setPodcastIndexSeason()`                            | Sets the tag [podcast:season](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/tags/season.md).  Expects an array with the required key `value` and the optional key `name`.                                                                                                                                                                                                                                                                                                                |
| `setPodcastIndexEpisode()`                           | Sets the tag [podcast:episode](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/tags/episode.md).  Expects an array with the required key `value` and the optional key `display`.                                                                                                                                                                                                                                                                                                           |
| `addPodcastIndexDetailedImage()`                     | Adds a [podcast:image](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/tags/image.md) tag.  Expects an array with the required key `href` and the optional keys `alt`, `aspectRatio`, `width`, `height`, `type` and `purpose`.                                                                                                                                                                                                                                                             |
| `setPodcastIndexDetailedImages()`                    | Sets multiple [podcast:image](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/tags/image.md) tags.  Expects an array of arrays, each with the required key `href` and the optional keys `alt`, `aspectRatio`, `width`, `height`, `type` and `purpose`.                                                                                                                                                                                                                                     |

# Examples
## Channel
### Channel Value

Please also consider the official PodcastIndex documentation on the
[value](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/tags/value.md),
and [valueRecipient](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/tags/value-recipient.md) namespaces.

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

## Episode
### Episode Value

Please consider the official PodcastIndex documentation on the
[value](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/tags/value.md),
[valueRecipient](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/tags/value-recipient.md)
and [valueTimeSplit](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/tags/value-time-split.md) namespaces.

#### Using `valueTimeSplit` with nested `remoteItem`

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

#### Using `valueTimeSplit` with nested `valueRecipient`

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

### Episode Alternate Enclosure

Please also consider the official PodcastIndex documentation on the
[alternateEnclosure](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/tags/alternate-enclosure.md),
[source](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/tags/source.md)
and [integrity](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/tags/integrity.md) namespaces.

#### With all attributes set

Usage:

```php
$sources = [
    [
        'uri' => 'https://example.com/file-720.torrent',
        'contentType' => 'application/x-bittorrent',
    ],
    [
        'uri' => 'ipfs://QmX33FYehk6ckGQ6g1D9D3FqZPix5JpKstKQKbaS8quUFb',
    ]
];
$integrity = [
    'type' => 'sri',
    'value' => 'sha384-ExVqijgYHm15PqQqdXfW95x+Rs6C+d6E/ICxyQOeFevnxNLR/wtJNrNYTjIysUBo',
];
$alternateEnclosure = [
    'type' => 'video/mp4',
    'length' => 7924786,
    'bitrate' => 511276.52,
    'height' => 720,
    'lang' => 'en',
    'title' => 'Standard',
    'rel' => 'default',
    'codecs' => 'avc1.42E01E, mp4a.40.2',
    'default' => true
];

$entryWriter->addPodcastIndexAlternateEnclosure($alternateEnclosure, $sources, $integrity);
```

Rendered alternate enclosure tag:

```xml
<podcast:alternateEnclosure type="video/mp4" length="7924786" bitrate="511276.52" height="720" lang="en" title="Standard" rel="default" codecs="avc1.42E01E, mp4a.40.2" default="true">
    <podcast:source uri="https://example.com/file-720.torrent" contentType="application/x-bittorrent"/>
    <podcast:source uri="ipfs://QmX33FYehk6ckGQ6g1D9D3FqZPix5JpKstKQKbaS8quUFb"/>
    <podcast:integrity type="sri" value="sha384-ExVqijgYHm15PqQqdXfW95x+Rs6C+d6E/ICxyQOeFevnxNLR/wtJNrNYTjIysUBo"/>
</podcast:alternateEnclosure>
```

#### With minimal data set

Usage:

```php
$sources = [
    ['uri' => 'ipfs://QmX33FYehk6ckGQ6g1D9D3FqZPix5JpKstKQKbaS8quUFb'],
];
$alternateEnclosure = [
    'type'    => 'video/mp4',
];

$entryWriter->addPodcastIndexAlternateEnclosure($alternateEnclosure, $sources, $integrity);
```

Rendered alternate enclosure tag:

```xml
<podcast:alternateEnclosure type="video/mp4">
    <podcast:source uri="ipfs://QmX33FYehk6ckGQ6g1D9D3FqZPix5JpKstKQKbaS8quUFb"/>
</podcast:alternateEnclosure>
```