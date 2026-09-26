<?php

declare(strict_types=1);

namespace LaminasApiSampleTest\Unit\Http;

use ArrayIterator;
use Laminas\Http\Header\HeaderInterface;
use Laminas\Http\PhpEnvironment\Response as HttpResponse;
use LaminasApiSample\Domains\Auth\Scope;
use LaminasApiSample\Http\JsonResponseFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Exception as PHPUnitException;
use PHPUnit\Framework\TestCase;

#[CoversClass(JsonResponseFactory::class)]
final class JsonResponseFactoryTest extends TestCase
{
    /** @throws PHPUnitException */
    private static function getHeaderValueByName(
        HttpResponse $response,
        string $name,
    ): string {
        $header = $response->getHeaders()->get($name);
        if ($header instanceof ArrayIterator) {
            /** @var HeaderInterface|null $current */
            $current = $header->current();
            $header = $current;
        }

        if (!$header instanceof HeaderInterface) {
            static::fail("Missing {$name} header");
        }

        return $header->getFieldValue();
    }

    /** @return array<string, mixed> */
    private static function decodeBody(HttpResponse $response): array
    {
        /** @var array<string, mixed> */
        return json_decode(
            $response->getBody(),
            true,
            flags: JSON_THROW_ON_ERROR,
        );
    }

    /** @return iterable<string, array{HttpResponse, int, int, string}> */
    public static function getErrorResponses(): iterable
    {
        yield 'bad request' => [
            JsonResponseFactory::badRequest(),
            400,
            2,
            'Invalid query',
        ];

        yield 'unsupported content type' => [
            JsonResponseFactory::unsupportedContentType(),
            400,
            5,
            'Unexpected content type',
        ];

        yield 'unauthorized' => [
            JsonResponseFactory::unauthorized(),
            401,
            8,
            'Unauthorized',
        ];

        yield 'forbidden' => [
            JsonResponseFactory::forbidden(Scope::AccountItemFilter->value),
            403,
            6,
            'Forbidden',
        ];

        yield 'not found' => [
            JsonResponseFactory::notFound(),
            404,
            1,
            'Resource not found',
        ];

        yield 'method not allowed' => [
            JsonResponseFactory::methodNotAllowed(['GET']),
            405,
            9,
            'Method not allowed',
        ];

        yield 'unprocessable' => [
            JsonResponseFactory::unprocessable('realm: bad'),
            422,
            10,
            'realm: bad',
        ];

        yield 'server error' => [
            JsonResponseFactory::serverError(),
            500,
            4,
            'Internal error',
        ];

        yield 'not implemented' => [
            JsonResponseFactory::notImplemented(),
            501,
            4,
            'Internal error',
        ];
    }

    /** @throws PHPUnitException */
    #[Test, DataProvider('getErrorResponses')]
    public function errorMatchesDocumentedShape(
        HttpResponse $response,
        int $status,
        int $code,
        string $message,
    ): void {
        static::assertSame($status, $response->getStatusCode());
        static::assertSame('application/json', self::getHeaderValueByName(
            $response,
            'Content-Type',
        ));
        static::assertSame(
            ['error' => ['code' => $code, 'message' => $message]],
            self::decodeBody($response),
        );
    }

    /** @throws PHPUnitException */
    #[Test]
    public function methodNotAllowedListsAllowedMethods(): void
    {
        $response = JsonResponseFactory::methodNotAllowed(['GET', 'POST']);

        static::assertSame('GET, POST', self::getHeaderValueByName(
            $response,
            'Allow',
        ));
    }

    /** @throws PHPUnitException */
    #[Test]
    public function assertOkResponseEncoded(): void
    {
        $data = ['filter' => ['id' => 'abc', 'public' => true]];
        $response = JsonResponseFactory::ok($data);

        static::assertSame(200, $response->getStatusCode());
        static::assertSame('application/json', self::getHeaderValueByName(
            $response,
            'Content-Type',
        ));
        static::assertSame($data, self::decodeBody($response));
    }

    /** @throws PHPUnitException */
    #[Test]
    public function authErrorsCarryWwwAuthenticate(): void
    {
        $message = <<<EOT
        Bearer error="invalid_token"
        EOT;

        static::assertSame($message, self::getHeaderValueByName(
            JsonResponseFactory::unauthorized(),
            'WWW-Authenticate',
        ));

        $message = <<<EOT
        Bearer error="insufficient_scope", scope="account:item_filter"
        EOT;

        static::assertSame($message, self::getHeaderValueByName(
            JsonResponseFactory::forbidden(Scope::AccountItemFilter->value),
            'WWW-Authenticate',
        ));
    }
}
