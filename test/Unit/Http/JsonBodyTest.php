<?php

declare(strict_types=1);

namespace LaminasApiSampleTest\Unit\Http;

use Laminas\Http\Headers;
use Laminas\Http\PhpEnvironment\Request as HttpRequest;
use LaminasApiSample\Http\Exceptions\MalformedJsonException;
use LaminasApiSample\Http\Exceptions\UnsupportedContentTypeException;
use LaminasApiSample\Http\JsonBody;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Throwable;

#[CoversClass(JsonBody::class)]
final class JsonBodyTest extends TestCase
{
    /** @throws RuntimeException */
    private static function request(
        ?string $contentType,
        string $content,
    ): HttpRequest {
        $request = new HttpRequest();
        if ($contentType !== null) {
            $headers = $request->getHeaders();
            static::assertInstanceOf(Headers::class, $headers);
            $headers->addHeaderLine('Content-Type', $contentType);
        }
        $request->setContent($content);

        return $request;
    }

    /** @throws RuntimeException */

    /**
     * @return iterable<string, array{HttpRequest, array<string, mixed>}>
     * @throws RuntimeException
     */
    public static function getValidBodies(): iterable
    {
        yield 'object' => [
            self::request('application/json', '{"a":1}'),
            ['a' => 1],
        ];

        yield 'charset parameter' => [
            self::request('application/json; charset=utf-8', '{"a":1}'),
            ['a' => 1],
        ];

        yield 'empty object' => [self::request('application/json', '{}'), []];
        yield 'leading whitespace' => [
            self::request('application/json', " \n{\"a\":1}"),
            ['a' => 1],
        ];
    }

    /**
     * @param array<string, mixed> $expected
     * @throws UnsupportedContentTypeException
     * @throws MalformedJsonException
     * @throws RuntimeException
     */
    #[Test, DataProvider('getValidBodies')]
    public function parsesJsonObject(
        HttpRequest $request,
        array $expected,
    ): void {
        static::assertSame($expected, JsonBody::parse($request));
    }

    /**
     * @return iterable<string, array{HttpRequest, class-string<Throwable>}>
     * @throws RuntimeException
     */
    public static function getInvalidBodies(): iterable
    {
        yield 'no content type' => [
            self::request(null, '{"a":1}'),
            UnsupportedContentTypeException::class,
        ];
        yield 'text/plain' => [
            self::request('text/plain', '{"a":1}'),
            UnsupportedContentTypeException::class,
        ];
        yield 'malformed' => [
            self::request('application/json', '{'),
            MalformedJsonException::class,
        ];
        yield 'empty body' => [
            self::request('application/json', ''),
            MalformedJsonException::class,
        ];
        yield 'list' => [
            self::request('application/json', '[1,2]'),
            MalformedJsonException::class,
        ];
        yield 'empty list' => [
            self::request('application/json', '[]'),
            MalformedJsonException::class,
        ];
        yield 'string' => [
            self::request('application/json', '"x"'),
            MalformedJsonException::class,
        ];
        yield 'number' => [
            self::request('application/json', '1'),
            MalformedJsonException::class,
        ];
    }

    /** @param class-string<Throwable> $exception */
    #[Test, DataProvider('getInvalidBodies')]
    public function rejectsInvalidBody(
        HttpRequest $request,
        string $exception,
    ): void {
        $this->expectException($exception);

        JsonBody::parse($request);
    }
}
