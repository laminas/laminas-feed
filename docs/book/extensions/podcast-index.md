# Podcast Index

The Podcast Index Extension adds support for the [Podcast Index RSS namespace](https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/1.0.md),
an open source project which consolidates new features for podcasts into a
single namespace.

Channel API methods:

Method | Description
------ | -----------
`isLocked()` | Returns whether the feed is open for importing to new platforms.
`getLockOwner()` | Returns the email address for owner verification.
`getFunding()` | Returns funding information. The output is an object with "url" and "value" properties.

Item API methods:

Method | Description
------ | -----------
`getTranscript()` | Returns transcript information for the entry. The output is an object with "url", "type", "language" and "rel" properties/
`getChapters()` | Returns chapter information for the entry. The output is an object with "url" and "type" properties.
`getSoundbites()` | Returns soundbites for the entry. The output is an array of objects with "title", "startTime" and "duration" properties.

See the [Podcast Index website](https://podcastindex.org) for more information
about the project.
