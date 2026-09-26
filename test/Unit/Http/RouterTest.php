<?php

declare(strict_types=1);

namespace LaminasApiSampleTest\Unit\Http;

use Laminas\Http\Header\HeaderInterface;
use Laminas\Http\PhpEnvironment\Request as HttpRequest;
use Laminas\Router\Http\Segment;
use Laminas\Router\Http\TreeRouteStack;
use Laminas\ServiceManager\ServiceManager;
use LaminasApiSample\Http\HandlerInterface;
use LaminasApiSample\Http\JsonResponseFactory;
use LaminasApiSample\Http\Router;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Exception as PHPUnitException;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;

#[
    CoversClass(Router::class),
    UsesClass(JsonResponseFactory::class),
    UsesClass(MockHandler::class),
]
final class RouterTest extends TestCase
{
    private static function stubRouter(HandlerInterface $handler): Router
    {
        $routes = new TreeRouteStack();
        $routes->addRoute(
            'character',
            new Segment(
                '/character/:id',
                ['id' => '[0-9]+'],
                ['handlers' => [
                    'GET' => 'character.read',
                    'POST' => 'character.missing',
                ]],
            ),
        );

        return new Router($routes, new ServiceManager(['services' => [
            'character.read' => $handler,
        ]]));
    }

    private static function buildStubRequest(
        string $method,
        string $path,
    ): HttpRequest {
        $request = new HttpRequest();
        $request->setMethod($method);
        $request->setUri($path);

        return $request;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws PHPUnitException
     */
    #[Test]
    public function unknownPathIsNotFound(): void
    {
        $response = self::stubRouter(new MockHandler())
            ->dispatch(self::buildStubRequest('GET', '/stash'));

        static::assertSame(404, $response->getStatusCode());
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws PHPUnitException
     */
    #[Test]
    public function paramFailingConstraintIsNotFound(): void
    {
        $response = self::stubRouter(new MockHandler())
            ->dispatch(self::buildStubRequest('GET', '/character/abc'));

        static::assertSame(404, $response->getStatusCode());
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws PHPUnitException
     */
    #[Test]
    public function unmappedMethodIsNotAllowed(): void
    {
        $response = self::stubRouter(new MockHandler())
            ->dispatch(self::buildStubRequest('DELETE', '/character/1'));

        static::assertSame(405, $response->getStatusCode());
        $allow = $response->getHeaders()->get('Allow');
        static::assertInstanceOf(HeaderInterface::class, $allow);
        /** @var HeaderInterface $allow */
        static::assertSame('GET, POST', $allow->getFieldValue());
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws PHPUnitException
     */
    #[Test]
    public function unregisteredHandlerIsNotImplemented(): void
    {
        $response = self::stubRouter(new MockHandler())
            ->dispatch(self::buildStubRequest('POST', '/character/1'));

        static::assertSame(501, $response->getStatusCode());
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws PHPUnitException
     */
    #[Test]
    public function matchedRouteCallsHandlerWithParamsOnly(): void
    {
        $handler = new MockHandler();
        $response = self::stubRouter($handler)
            ->dispatch(self::buildStubRequest('GET', '/character/1'));

        static::assertSame(200, $response->getStatusCode());
        static::assertSame(['id' => '1'], $handler->received);
    }
}
