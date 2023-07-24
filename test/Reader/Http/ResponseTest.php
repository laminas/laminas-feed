<?php

declare(strict_types=1);

namespace LaminasTest\Feed\Reader\Http;

use Laminas\Feed\Reader\Exception\InvalidArgumentException;
use Laminas\Feed\Reader\Http\Response;
use LaminasTest\Feed\Reader\TestAsset\Psr7Stream;
use PHPUnit\Framework\TestCase;

use function var_export;

/**
 * @covers \Laminas\Feed\Reader\Http\Response
 */
class ResponseTest extends TestCase
{
    public function testConstructorOnlyRequiresStatusCode(): void
    {
        $response = new Response(200);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('', $response->getBody());
    }

    public function testConstructorCanAcceptResponseBody(): void
    {
        $response = new Response(201, 'CREATED');
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals('CREATED', $response->getBody());
    }

    public function testConstructorCanAcceptAStringCastableObjectForTheResponseBody(): void
    {
        $stream   = new Psr7Stream('BODY');
        $response = new Response(200, $stream);
        $this->assertEquals('BODY', $response->getBody());
    }

    public function testConstructorCanAcceptHeaders(): void
    {
        $response = new Response(204, '', [
            'Location'         => 'http://example.org/foo',
            'Content-Length'   => 1234,
            'X-Content-Length' => 1234.56,
        ]);
        $this->assertEquals(204, $response->getStatusCode());
        $this->assertEquals('', $response->getBody());
        $this->assertEquals('http://example.org/foo', $response->getHeaderLine('Location'));
        $this->assertEquals(1234, $response->getHeaderLine('Content-Length'));
        $this->assertEquals(1234.56, $response->getHeaderLine('X-Content-Length'));
    }

    /** @psalm-return iterable<int|string, array{0: mixed, 1: string}> */
    public static function invalidStatusCodes(): iterable
    {
        foreach ([-100, 0, 1, 99] as $statusCode) {
            yield $statusCode => [$statusCode, 'between 100 and 599'];
        }

        foreach ([600, 700, 1000] as $statusCode) {
            yield $statusCode => [$statusCode, 'between 100 and 599'];
        }

        foreach ([100.1, 599.1] as $statusCode) {
            yield var_export($statusCode, true) => [$statusCode, 'integer status code'];
        }

        $invalidTypes = [
            'null'   => [null, 'numeric status code'],
            'true'   => [true, 'numeric status code'],
            'false'  => [false, 'numeric status code'],
            'string' => [' 100 ', 'numeric status code'],
            'array'  => [[200], 'numeric status code'],
            'object' => [(object) [100], 'numeric status code'],
        ];
        foreach ($invalidTypes as $key => $value) {
            yield $key => $value;
        }
    }

    /**
     * @dataProvider invalidStatusCodes
     * @param mixed $statusCode
     */
    public function testConstructorRaisesExceptionForInvalidStatusCode($statusCode, string $contains)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($contains);
        new Response($statusCode);
    }

    /** @psalm-return array<string, array{0: mixed}> */
    public static function invalidBodies(): array
    {
        return [
            'null'       => [null],
            'true'       => [true],
            'false'      => [false],
            'zero'       => [0],
            'int'        => [1],
            'zero-float' => [0.0],
            'float'      => [1.1],
            'array'      => [['BODY']],
            'object'     => [(object) ['body' => 'BODY']],
        ];
    }

    /**
     * @dataProvider invalidBodies
     * @param mixed $body
     */
    public function testConstructorRaisesExceptionForInvalidBody($body)
    {
        $this->expectException(InvalidArgumentException::class);
        new Response(200, $body);
    }

    /** @psalm-return array<string, array{0: array<array-key, mixed>, 1: string}> */
    public static function invalidHeaders(): array
    {
        return [
            'empty-name'   => [
                ['' => 'value'],
                'non-empty, non-numeric',
            ],
            'zero-name'    => [
                ['value'],
                'non-empty, non-numeric',
            ],
            'int-name'     => [
                [1 => 'value'],
                'non-empty, non-numeric',
            ],
            'numeric-name' => [
                ['1.1' => 'value'],
                'non-empty, non-numeric',
            ],
            'null-value'   => [
                ['X-Test' => null],
                'must be a string or numeric',
            ],
            'true-value'   => [
                ['X-Test' => true],
                'must be a string or numeric',
            ],
            'false-value'  => [
                ['X-Test' => false],
                'must be a string or numeric',
            ],
            'array-value'  => [
                ['X-Test' => ['BODY']],
                'must be a string or numeric',
            ],
            'object-value' => [
                ['X-Test' => (object) ['body' => 'BODY']],
                'must be a string or numeric',
            ],
        ];
    }

    /**
     * @dataProvider invalidHeaders
     */
    public function testConstructorRaisesExceptionForInvalidHeaderStructures(array $headers, string $contains)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($contains);
        new Response(200, '', $headers);
    }

    public function testRetrievingHeaderLineWithDefaultValueReturnsDefaultValueWhenHeaderIsNotFound(): void
    {
        $response = new Response(200);
        $this->assertSame('DEFAULT', $response->getHeaderLine('X-Not-Found', 'DEFAULT'));
    }
}
