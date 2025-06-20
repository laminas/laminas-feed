# Podcast Index

The Podcast Index Extension adds support for
the [Podcast Index RSS namespace](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/1.0.md),
an open source project which consolidates new features for podcasts into a
single namespace.

## Channel API

### GET methods

| Method                                        | Description                                                                                                                                        |
|-----------------------------------------------|----------------------------------------------------------------------------------------------------------------------------------------------------|
| `isLocked()`                                  | Returns whether the feed is open for importing to new platforms.                                                                                   |
| `getLockOwner()` `getPodcastIndexLockOwner()` | Returns the email address for owner verification.                                                                                                  |
| `getFunding()` `getPodcastIndexFunding()`     | Returns funding information. The output is an object with "url" and "value" properties.                                                            |
| `getPodcastIndexLicense()`                    | Returns license information. The output is an object with "identifier" and "url" properties.                                                       |
| `getPodcastIndexLocation()`                   | Returns funding information. The output is an object with "description", "geo" and "osm" properties.                                               |
| `getPodcastIndexImages()`                     | Returns information on responsive images. The output is an object with a "srcset" property.                                                        |
| `getPodcastIndexUpdateFrequency()`            | Returns information on the intended release schedule. The output is an object with "description", "complete", "dtstart" and "rrule" properties.    |
| `getPodcastIndexPeople()`                    | Returns information on the involved people. The output is an array of objects, each with the properties "name", "role", "group", "img" and "href". |

### SET methods

| Method                             | Description                                                                                                                           |
|------------------------------------|---------------------------------------------------------------------------------------------------------------------------------------|
| `setPodcastIndexLocked()`          | Expects an array with the required keys "value" and "owner".                                                                          |
| `setPodcastIndexFunding()`         | Expects an array with the required keys "title" and "url".                                                                            |
| `setPodcastIndexLicense()`         | Expects an array with the required keys "identifier" and "url".                                                                       |
| `setPodcastIndexLocation()`        | Expects an array with the required key "description" and the optional keys "geo" and "osm".                                           |
| `setPodcastIndexImages()`          | Expects an array with the required key "srcset".                                                                                      |
| `setPodcastIndexUpdateFrequency()` | Expects an array with the required key "description" and the optional keys "complete" (bool), "dtstart" (ISO8601 string) and "rrule". |
| `addPodcastIndexPerson()`          | Expects an array with the required key "name" and the optional keys "role", "group", "img" and "href".                                |
| `setPodcastIndexPeople()`         | Expects an array of objects with each the required key "name" and the optional keys "role", "group", "img" and "href".                |

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

See the [Podcast Index website](https://podcastindex.org) for more information
about the project.
