<?php

declare(strict_types=1);

namespace LaminasTest\Feed\PubSubHubbub;

use PHPUnit\Framework\Attributes\BackupGlobals;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

use function array_merge;

#[BackupGlobals(true)]
class AbstractCallbackTest extends TestCase
{
    public function testDetectCallbackUrlIgnoresXOriginalUrlHeaderWhenXRewriteUrlHeaderIsNotPresent(): void
    {
        $_SERVER = array_merge($_SERVER, [
            'HTTP_X_ORIGINAL_URL' => '/hijack-attempt',
            'HTTPS'               => 'on',
            'HTTP_HOST'           => 'example.com',
            'REQUEST_URI'         => '/requested/path',
        ]);

        $callback = new TestAsset\Callback();

        $r = new ReflectionMethod($callback, '_detectCallbackUrl');

        $this->assertSame('/requested/path', $r->invoke($callback));
    }

    public function testDetectCallbackUrlRequiresCombinationOfIISWasUrlRewrittenAndUnencodedUrlToReturnEarly(): void
    {
        $_SERVER = array_merge($_SERVER, [
            'IIS_WasUrlRewritten' => '1',
            'UNENCODED_URL'       => '/requested/path',
        ]);

        $callback = new TestAsset\Callback();

        $r = new ReflectionMethod($callback, '_detectCallbackUrl');

        $this->assertSame('/requested/path', $r->invoke($callback));
    }

    public function testDetectCallbackUrlUsesRequestUriWhenNoOtherRewriteHeadersAreFound(): void
    {
        $_SERVER = array_merge($_SERVER, [
            'REQUEST_URI' => '/expected/path',
        ]);

        $callback = new TestAsset\Callback();

        $r = new ReflectionMethod($callback, '_detectCallbackUrl');

        $this->assertSame('/expected/path', $r->invoke($callback));
    }

    public function testDetectCallbackUrlFallsBackToOrigPathInfoWhenAllElseFails(): void
    {
        $_SERVER = array_merge($_SERVER, [
            'ORIG_PATH_INFO' => '/expected/path',
        ]);

        $callback = new TestAsset\Callback();

        $r = new ReflectionMethod($callback, '_detectCallbackUrl');

        $this->assertSame('/expected/path', $r->invoke($callback));
    }

    public function testDetectCallbackReturnsEmptyStringIfNoResourcesMatchedInServerSuperglobal(): void
    {
        $callback = new TestAsset\Callback();

        $r = new ReflectionMethod($callback, '_detectCallbackUrl');

        $this->assertSame('', $r->invoke($callback));
    }
}
