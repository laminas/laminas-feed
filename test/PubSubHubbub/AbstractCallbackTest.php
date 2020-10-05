<?php

/**
 * @see       https://github.com/laminas/laminas-feed for the canonical source repository
 * @copyright https://github.com/laminas/laminas-feed/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-feed/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Feed\PubSubHubbub;

use PHPUnit\Framework\TestCase;
use ReflectionMethod;

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
        $r->setAccessible(true);

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
        $r->setAccessible(true);

        $this->assertSame('/requested/path', $r->invoke($callback));
    }

    public function testDetectCallbackUrlUsesRequestUriWhenNoOtherRewriteHeadersAreFound(): void
    {
        $_SERVER = array_merge($_SERVER, [
            'REQUEST_URI' => '/expected/path',
        ]);

        $callback = new TestAsset\Callback();

        $r = new ReflectionMethod($callback, '_detectCallbackUrl');
        $r->setAccessible(true);

        $this->assertSame('/expected/path', $r->invoke($callback));
    }

    public function testDetectCallbackUrlFallsBackToOrigPathInfoWhenAllElseFails(): void
    {
        $_SERVER = array_merge($_SERVER, [
            'ORIG_PATH_INFO' => '/expected/path',
        ]);

        $callback = new TestAsset\Callback();

        $r = new ReflectionMethod($callback, '_detectCallbackUrl');
        $r->setAccessible(true);

        $this->assertSame('/expected/path', $r->invoke($callback));
    }

    public function testDetectCallbackReturnsEmptyStringIfNoResourcesMatchedInServerSuperglobal(): void
    {
        $callback = new TestAsset\Callback();

        $r = new ReflectionMethod($callback, '_detectCallbackUrl');
        $r->setAccessible(true);

        $this->assertSame('', $r->invoke($callback));
    }
}
