<?php

declare(strict_types=1);

use Boundwize\StructArmed\Architecture;
use Boundwize\StructArmed\Preset\Preset;
use Boundwize\StructArmed\Rule\Rules\Class_\MustBeFinalRule;

return Architecture::define()
    ->skipPath(__DIR__ . '/test/Reader/_files')
    ->withPresets(Preset::PSR4(), Preset::CODEQUALITY())
    ->layer('Exception', 'src/Exception')
    ->layer('Uri', 'src/Uri.php')
    ->layer('ReaderException', 'src/Reader/Exception')
    ->layer('ReaderHttp', 'src/Reader/Http')
    ->layer('ReaderCollection', [
        'src/Reader/Collection.php',
        'src/Reader/Collection',
    ])
    ->layer('Reader', 'src/Reader', [
        'src/Reader/Collection',
        'src/Reader/Exception',
        'src/Reader/Http',
    ])
    ->layer('WriterException', 'src/Writer/Exception')
    ->layer('WriterRenderer', 'src/Writer/Renderer')
    ->layer('WriterExtension', 'src/Writer/Extension')
    ->layer('Writer', 'src/Writer', [
        'src/Writer/Exception',
        'src/Writer/Extension',
        'src/Writer/Renderer',
    ])
    ->layer('PubSubHubbubException', 'src/PubSubHubbub/Exception')
    ->layer('PubSubHubbub', 'src/PubSubHubbub', 'src/PubSubHubbub/Exception')
    ->ruleset([
        'Exception'             => [],
        'Uri'                   => [],
        'ReaderException'       => ['Exception'],
        'ReaderHttp'            => ['+ReaderException'],
        'ReaderCollection'      => [],
        'Reader'                => ['+ReaderHttp', 'ReaderCollection', 'Uri'],
        'WriterException'       => ['Exception'],
        'WriterRenderer'        => ['+WriterException', 'Writer', 'Uri'],
        'WriterExtension'       => ['+WriterRenderer'],
        'Writer'                => ['+WriterException', 'WriterExtension', 'Uri'],
        'PubSubHubbubException' => ['Exception'],
        'PubSubHubbub'          => ['+PubSubHubbubException', 'Reader', 'Uri'],
    ])

    ->rule(
        'tests_classes.must_be_final',
        new MustBeFinalRule(layer: 'tests')
    )
    ->layer('tests', 'test');
