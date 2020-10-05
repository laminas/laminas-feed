<?php

/**
 * @see       https://github.com/laminas/laminas-feed for the canonical source repository
 * @copyright https://github.com/laminas/laminas-feed/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-feed/blob/master/LICENSE.md New BSD License
 */

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

    /**
     * @return \Generator
     *
     * @psalm-return \Generator<float|int|string, array{0: \stdClass|array{0: int}|bool|float|int|null|string, 1: string}, mixed, void>
     */
    public function invalidStatusCodes(): \Generator
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
     *
     * @return void
     */
    public function testConstructorRaisesExceptionForInvalidStatusCode($statusCode, $contains): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($contains);
        new Response($statusCode);
    }

    /**
     * @return (\stdClass|bool|float|int|null|string[])[][]
     *
     * @psalm-return array{null: array{0: null}, true: array{0: true}, false: array{0: false}, zero: array{0: int}, int: array{0: int}, zero-float: array{0: float}, float: array{0: float}, array: array{0: array{0: string}}, object: array{0: \stdClass}}
     */
    public function invalidBodies(): array
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
     *
     * @return void
     */
    public function testConstructorRaisesExceptionForInvalidBody($body): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Response(200, $body);
    }

    /**
     * @return ((\stdClass|bool|null|string|string[])[]|string)[][]
     *
     * @psalm-return array{empty-name: array{0: array{: string}, 1: string}, zero-name: array{0: array{0: string}, 1: string}, int-name: array{0: array{1: string}, 1: string}, numeric-name: array{0: array{'1.1': string}, 1: string}, null-value: array{0: array{X-Test: null}, 1: string}, true-value: array{0: array{X-Test: true}, 1: string}, false-value: array{0: array{X-Test: false}, 1: string}, array-value: array{0: array{X-Test: array{0: string}}, 1: string}, object-value: array{0: array{X-Test: \stdClass}, 1: string}}
     */
    public function invalidHeaders(): array
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
     *
     * @return void
     */
    public function testConstructorRaisesExceptionForInvalidHeaderStructures($headers, $contains): void
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
