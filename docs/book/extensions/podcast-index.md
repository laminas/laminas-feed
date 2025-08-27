# Podcast Index

The Podcast Index Extension adds support for
the [Podcast Index RSS namespace](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/1.0.md),
an open source project which consolidates new features for podcasts into a
single namespace.

See the [Podcast Index website](https://podcastindex.org) for more information about the project.

## Channel API

### GET methods

| Method                                        | Description                                                                                                                                                                                                                                                                                                            |
|-----------------------------------------------|------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `isLocked()` `isPodcastIndexLocked()`         | Returns whether the feed is open for importing to new platforms.                                                                                                                                                                                                                                                       |
| `getLockOwner()` `getPodcastIndexLockOwner()` | Returns the email address for owner verification.                                                                                                                                                                                                                                                                      |
| `getFunding()` `getPodcastIndexFunding()`     | Returns funding information. The output is an object with "url" and "value" properties.                                                                                                                                                                                                                                |
| `getPodcastIndexLicense()`                    | Returns license information. The output is an object with "identifier" and "url" properties.                                                                                                                                                                                                                           |
| `getPodcastIndexLocation()`                   | Returns location information. The output is an object with "description", "geo" and "osm" properties.                                                                                                                                                                                                                  |
| `getPodcastIndexImages()`                     | Returns information on responsive images. The output is an object with a "srcset" property.                                                                                                                                                                                                                            |
| `getPodcastIndexUpdateFrequency()`            | Returns information on the intended release schedule. The output is an object with "description", "complete", "dtstart" and "rrule" properties.                                                                                                                                                                        |
| `getPodcastIndexPeople()`                     | Returns information on the involved people. The output is an array of objects, each with the properties "name", "role", "group", "img" and "href".                                                                                                                                                                     |
| `getPodcastIndexTrailer()`                    | Returns information on the podcast trailer. The output is an object with the properties "title", "pubdate", "url", "length", "type" and "season".                                                                                                                                                                      |
| `getPodcastIndexGuid()`                       | Returns the podcast guid. The output is an object with the property "value".                                                                                                                                                                                                                                           |
| `getPodcastIndexMedium()`                     | Returns the podcast medium. The output is an object with the property "value".                                                                                                                                                                                                                                         |
| `getPodcastIndexBlocks()`                     | Returns whether or which platforms are allowed to publicly display this feed. The output is an object with the properties "value" and "id".                                                                                                                                                                            |
| `getPodcastIndexTxts()`                       | Returns information on topics that do not have their own tags. The output is an object with the properties "value" and "purpose".                                                                                                                                                                                      |
| `getPodcastIndexPodping()`                    | Returns whether the feed sends out Podping notifications when changes are made to it. The output is an object with the property "usesPodping".                                                                                                                                                                         |
| `getPodcastIndexRemoteItems()`                | Returns the remote items assigned as direct children of the feed. The output is an array of objects with each the properties "feedGuid", "feedUrl", "itemGuid", "medium" and "title". <em>Note: Nested remote items that belong to other namespaces need to be managed with the methods of those namespaces.</em>      |
| `getPodcastIndexPodroll()`                    | Returns the remote items assigned to the podroll tag of the feed. The output is an array of objects with each the properties "feedGuid", "feedUrl", "itemGuid", "medium" and "title".                                                                                                                                  |
| `getPodcastIndexPublisher()`                  | Returns one remote item assigned to the publisher tag of the feed. The output is an object with the properties "feedGuid", "feedUrl", "itemGuid", "medium" and "title".                                                                                                                                                |
| `getPodcastIndexValues()`                     | Returns the values and their value recipients. The output is an array of objects, each with the properties "type", "method", "suggested" and "recipients". "recipients" is an array of objects itself, each object containing the attributes "name", "type", "address", "split", "customKey", "customValue" and "fee". |

### SET methods

| Method                               | Description                                                                                                                                                                                                                                                                                                                                                                                                                                       |
|--------------------------------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `setPodcastIndexLocked()`            | Expects an array with the required keys "value" and "owner".                                                                                                                                                                                                                                                                                                                                                                                      |
| `setPodcastIndexFunding()`           | Expects an array with the required keys "title" and "url".                                                                                                                                                                                                                                                                                                                                                                                        |
| `setPodcastIndexLicense()`           | Expects an array with the required keys "identifier" and "url".                                                                                                                                                                                                                                                                                                                                                                                   |
| `setPodcastIndexLocation()`          | Expects an array with the required key "description" and the optional keys "geo" and "osm".                                                                                                                                                                                                                                                                                                                                                       |
| `setPodcastIndexImages()`            | Expects an array with the required key "srcset".                                                                                                                                                                                                                                                                                                                                                                                                  |
| `setPodcastIndexUpdateFrequency()`   | Expects an array with the required key "description" and the optional keys "complete" (bool), "dtstart" (ISO8601 string) and "rrule".                                                                                                                                                                                                                                                                                                             |
| `addPodcastIndexPerson()`            | Expects an array with the required key "name" and the optional keys "role", "group", "img" and "href".                                                                                                                                                                                                                                                                                                                                            |
| `setPodcastIndexPeople()`            | Expects an array of arrays with each the required key "name" and the optional keys "role", "group", "img" and "href".                                                                                                                                                                                                                                                                                                                             |
| `setPodcastIndexTrailer()`           | Expects an array with the required keys "title", "pubdate" and "url" and the optional keys "length", "type" and "season".                                                                                                                                                                                                                                                                                                                         |
| `setPodcastIndexGuid()`              | Expects an array with the required key "value".                                                                                                                                                                                                                                                                                                                                                                                                   |
| `setPodcastIndexMedium()`            | Expects an array with the required key "value".                                                                                                                                                                                                                                                                                                                                                                                                   |
| `addPodcastIndexBlock()`             | Expects an array with the required key "value" and the optional key "id".                                                                                                                                                                                                                                                                                                                                                                         |
| `setPodcastIndexBlocks()`            | Expects an array of arrays with each the required key "value" and the optional key "id".                                                                                                                                                                                                                                                                                                                                                          |
| `addPodcastIndexTxt()`               | Expects an array with the required key "value" and the optional key "purpose".                                                                                                                                                                                                                                                                                                                                                                    |
| `setPodcastIndexTxts()`              | Expects an array of arrays with each the required key "value" and the optional key "purpose".                                                                                                                                                                                                                                                                                                                                                     |
| `setPodcastIndexPodping()`           | Expects an array with the required key "usesPodping".                                                                                                                                                                                                                                                                                                                                                                                             |
| `addPodcastIndexRemoteItem()`        | Expects an array with the required key "feedGuid" and the optional keys "feedUrl", "itemGuid", "medium" and "title".                                                                                                                                                                                                                                                                                                                              |
| `setPodcastIndexRemoteItems()`       | Expects an array of arrays with each the required key "feedGuid" and the optional keys "feedUrl", "itemGuid", "medium" and "title". <em>Note: Nested remote items that belong to other namespaces need to be managed with the methods of those namespaces.</em>                                                                                                                                                                                   |
| `setPodcastIndexPodroll()`           | Expects an array of remote items arrays with each the required key "feedGuid" and the optional keys "feedUrl", "itemGuid", "medium" and "title".                                                                                                                                                                                                                                                                                                  |
| `addPodcastIndexPodrollRemoteItem()` | Expects an array with the required key "feedGuid" and the optional keys "feedUrl", "itemGuid", "medium" and "title".                                                                                                                                                                                                                                                                                                                              |
| `setPodcastIndexPublisher()`         | Expects an array with the required key "feedGuid" and the optional keys "feedUrl", "itemGuid", "medium" and "title".                                                                                                                                                                                                                                                                                                                              |
| `addPodcastIndexValue()`             | Expects two arguments: First, an array of the value attributes, containing the required keys "type" and "method" and the optional key "suggested". As second argument, and array of one or more value recipient entries is expected. Each entry must be of type array, containing the required keys "type", "address" and "split" and the optional keys "name", "customKey", "customValue" and "fee". You may consider the example further below. |
| `resetPodcastIndexValues()`          | Expects no arguments. Removes all value entries from the feed writer.                                                                                                                                                                                                                                                                                                                                                                             |

## Episode API

### GET methods

| Method            | Description                                                                                                                |
|-------------------|----------------------------------------------------------------------------------------------------------------------------|
| `getTranscript()` | Returns transcript information for the entry. The output is an object with "url", "type", "language" and "rel" properties. |
| `getChapters()`   | Returns chapter information for the entry. The output is an object with "url" and "type" properties.                       |
| `getSoundbites()` | Returns soundbites for the entry. The output is an array of objects with "title", "startTime" and "duration" properties.   |

### SET methods

| Method                        | Description                                                                                                                                       |
|-------------------------------|---------------------------------------------------------------------------------------------------------------------------------------------------|
| `setPodcastIndexTranscript()` | Expects an array with the required keys "url" and "type", and with the optional keys "language" and "rel".                                        |
| `setPodcastIndexChapters()`   | Expects an array with the required keys "url" and "type".                                                                                         |
| `addPodcastIndexSoundbites()` | Expects an array of soundbite entries, each itself an array with the required keys "title" and "startTime", and with the optional key "duration". |




## Examples

### Channel: addPodcastIndexValue()

Usage:

    $value = [
        'type'      => "lightning",
        'method'    => "keysend",
        'suggested' => 0.00000005000,
    ];
    $recipients = [
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
    $feedWriter->addPodcastIndexValue($value, $recipients);

Rendered outcome:

    <podcast:value type="lightning" method="keysend" suggested="0.00000005000">
        <podcast:recipient name="Alice (Podcaster)" type="node" address="02d5c1bf8b940dc9cadca86d1b0a3c37fbe39cee4c7e839e33bef9174531d27f52" split="40"/>
        <podcast:recipient name="Bob (Podcaster)" type="node" address="032f4ffbbafffbe51726ad3c164a3d0d37ec27bc67b29a159b0f49ae8ac21b8508" split="60"/>
    </podcast:value>