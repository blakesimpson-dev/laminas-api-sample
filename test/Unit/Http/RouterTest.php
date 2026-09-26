<?php

declare(strict_types=1);

namespace LaminasApiSampleTest\Unit\Http;

use Laminas\Http\Header\HeaderInterface;
use Laminas\Http\PhpEnvironment\Request as HttpRequest;
use Laminas\Router\Http\Segment;
use Laminas\Router\Http\TreeRouteStack;
use Laminas\ServiceManager\ServiceManager;
use LaminasApiSample\Domains\Auth\AccessTokenEntity;
use LaminasApiSample\Domains\Profile\ProfileEntity;
use LaminasApiSample\Http\Auth\AuthContext;
use LaminasApiSample\Http\Auth\AuthenticatorInterface;
use LaminasApiSample\Http\HandlerInterface;
use LaminasApiSample\Http\JsonResponseFactory;
use LaminasApiSample\Http\Router;
use LaminasApiSampleTest\Support\FixedTime;
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
    UsesClass(MockAuthenticator::class),
    UsesClass(AccessTokenEntity::class),
    UsesClass(ProfileEntity::class),
    UsesClass(AuthContext::class),
]
// @mago-expect lint:too-many-methods
final class RouterTest extends TestCase
{
    public const string TEST_SCOPE = 'test:scope';

    private static function stubRouter(
        HandlerInterface $handler,
        AuthenticatorInterface $authenticator,
        ?string $scope = self::TEST_SCOPE,
    ): Router {
        $defaults = ['handlers' => [
            'GET' => 'character.read',
            'POST' => 'character.missing',
        ]];
        if ($scope !== null) {
            $defaults['scope'] = $scope;
        }

        $testRoute = new Segment(
            route: '/character/:id',
            constraints: ['id' => '[0-9]+'],
            defaults: $defaults,
        );

        $routeStack = new TreeRouteStack()->addRoute(
            name: 'character',
            route: $testRoute,
        );

        return new Router(
            $routeStack,
            new ServiceManager(['services' => [
                'character.read' => $handler,
            ]]),
            $authenticator,
        );
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

    /** @param list<string> $scopes */
    private static function stubToken(array $scopes = [
        self::TEST_SCOPE,
    ]): AccessTokenEntity
    {
        return new AccessTokenEntity(
            createdAt: FixedTime::getForCreate(),
            // @mago-expect lint:no-literal-password
            plainToken: 'test-token',
            scopes: $scopes,
            expiresAt: FixedTime::getForUpdate(),
            profile: new ProfileEntity(
                createdAt: FixedTime::getForCreate(),
                name: 'Test',
            ),
        );
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws PHPUnitException
     */
    #[Test]
    public function unknownPathIsNotFound(): void
    {
        $response = self::stubRouter(
            new MockHandler(),
            new MockAuthenticator(self::stubToken()),
        )
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
        $response = self::stubRouter(
            new MockHandler(),
            new MockAuthenticator(self::stubToken()),
        )
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
        $response = self::stubRouter(
            new MockHandler(),
            new MockAuthenticator(self::stubToken()),
        )
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
        $response = self::stubRouter(
            new MockHandler(),
            new MockAuthenticator(self::stubToken()),
        )
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
        $response = self::stubRouter(
            $handler,
            new MockAuthenticator(self::stubToken()),
        )
            ->dispatch(self::buildStubRequest('GET', '/character/1'));

        static::assertSame(200, $response->getStatusCode());
        static::assertSame(['id' => '1'], $handler->receivedParams);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws PHPUnitException
     */
    #[Test]
    public function failedAuthenticationIsUnauthorized(): void
    {
        $response = self::stubRouter(
            new MockHandler(),
            new MockAuthenticator(null),
        )
            ->dispatch(self::buildStubRequest('GET', '/character/1'));

        static::assertSame(401, $response->getStatusCode());
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws PHPUnitException
     */
    #[Test]
    public function tokenWithoutRouteScopeIsForbidden(): void
    {
        $response = self::stubRouter(
            new MockHandler(),
            new MockAuthenticator(self::stubToken(['other:scope'])),
        )
            ->dispatch(self::buildStubRequest('GET', '/character/1'));

        static::assertSame(403, $response->getStatusCode());
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws PHPUnitException
     */
    #[Test]
    public function routeWithoutScopeFailsClosed(): void
    {
        $response = self::stubRouter(
            new MockHandler(),
            new MockAuthenticator(self::stubToken()),
            scope: null,
        )
            ->dispatch(self::buildStubRequest('GET', '/character/1'));

        static::assertSame(500, $response->getStatusCode());
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws PHPUnitException
     */
    #[Test]
    public function handlerReceivesAuthContextForTokenProfile(): void
    {
        $handler = new MockHandler();
        $token = self::stubToken();

        self::stubRouter($handler, new MockAuthenticator($token))
            ->dispatch(self::buildStubRequest('GET', '/character/1'));

        $auth = $handler->receivedAuth;
        if ($auth === null) {
            static::fail('handler should receive an AuthContext');
        }

        static::assertSame($token->getProfile(), $auth->profile);
        static::assertSame([self::TEST_SCOPE], $auth->scopes);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws PHPUnitException
     */
    #[Test]
    public function methodIsCheckedBeforeAuthentication(): void
    {
        $response = self::stubRouter(
            new MockHandler(),
            new MockAuthenticator(null),
        )
            ->dispatch(self::buildStubRequest('DELETE', '/character/1'));

        static::assertSame(405, $response->getStatusCode());
    }
}
