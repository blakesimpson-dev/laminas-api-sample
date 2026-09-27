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
use LaminasApiSample\Http\RateLimiting\CounterStoreInterface;
use LaminasApiSample\Http\RateLimiting\InMemoryCounterStore;
use LaminasApiSample\Http\RateLimiting\RateLimiter;
use LaminasApiSample\Http\RateLimiting\RateLimitRule;
use LaminasApiSample\Http\Router;
use LaminasApiSampleTest\Support\FixedTime;
use LaminasApiSampleTest\Support\GetHeaderValue;
use LaminasApiSampleTest\Support\MutableClock;
use LaminasApiSampleTest\Support\StubAuthenticator;
use LaminasApiSampleTest\Support\StubHandler;
use LaminasApiSampleTest\Support\TestProfile;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Exception as PHPUnitException;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;

#[
    CoversClass(Router::class),
    UsesClass(JsonResponseFactory::class),
    UsesClass(StubHandler::class),
    UsesClass(StubAuthenticator::class),
    UsesClass(AccessTokenEntity::class),
    UsesClass(ProfileEntity::class),
    UsesClass(AuthContext::class),
]
// @mago-expect lint:too-many-methods
final class RouterTest extends TestCase
{
    public const string TEST_SCOPE = 'test:scope';

    private static function stubCounterStore(): CounterStoreInterface
    {
        return new InMemoryCounterStore(new MutableClock(FixedTime::now()));
    }

    private static function stubRateLimiter(?RateLimitRule $rule = null): RateLimiter
    {
        return new RateLimiter(
            store: self::stubCounterStore(),
            policy: 'api',
            rule: $rule ?? new RateLimitRule(
                name: 'client',
                maxHits: 10,
                periodSeconds: 5,
                restrictSeconds: 10,
            ),
        );
    }

    private static function stubRouter(
        HandlerInterface $handler,
        AuthenticatorInterface $authenticator,
        ?RateLimiter $rateLimiter = null,
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

        $serviceManager = new ServiceManager(['services' => [
            'character.read' => $handler,
        ]]);

        return new Router(
            $routeStack,
            $serviceManager,
            $authenticator,
            $rateLimiter ?? self::stubRateLimiter(),
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
            createdAt: FixedTime::inThePast(),
            profile: TestProfile::new(),
            // @mago-expect lint:no-literal-password
            plainToken: 'test-token',
            scopes: $scopes,
            expiresAt: FixedTime::now(),
        );
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws PHPUnitException
     */
    #[Test]
    public function requestCarriesRateLimitingHeaders(): void
    {
        $response = self::stubRouter(
            handler: new StubHandler(),
            authenticator: new StubAuthenticator(self::stubToken()),
        )
            ->dispatch(self::buildStubRequest('GET', '/character/1'));

        static::assertSame(200, $response->getStatusCode());
        static::assertSame('api', GetHeaderValue::byName(
            $response,
            'X-Rate-Limit-Policy',
        ));
        static::assertSame('client', GetHeaderValue::byName(
            $response,
            'X-Rate-Limit-Rules',
        ));
        static::assertSame('10:5:10', GetHeaderValue::byName(
            $response,
            'X-Rate-Limit-Client',
        ));
        static::assertSame('1:5:0', GetHeaderValue::byName(
            $response,
            'X-Rate-Limit-Client-State',
        ));
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws PHPUnitException
     */
    #[Test]
    public function requestLimitedAfterFirstHit(): void
    {
        $rateLimiterRule = new RateLimitRule(
            name: 'client',
            maxHits: 1,
            periodSeconds: 5,
            restrictSeconds: 10,
        );
        $rateLimiter = self::stubRateLimiter($rateLimiterRule);

        $router = self::stubRouter(
            handler: new StubHandler(),
            authenticator: new StubAuthenticator(self::stubToken()),
            rateLimiter: $rateLimiter,
        );

        $request = self::buildStubRequest('GET', '/character/1');
        $router->dispatch($request);

        $secondResponse = $router->dispatch($request);

        static::assertSame(429, $secondResponse->getStatusCode());
        static::assertSame('10', GetHeaderValue::byName(
            $secondResponse,
            'Retry-After',
        ));
        static::assertSame(
            ['error' => ['code' => 3, 'message' => 'Rate limit exceeded']],
            json_decode(
                $secondResponse->getBody(),
                true,
                flags: JSON_THROW_ON_ERROR,
            ),
        );

        static::assertSame('api', GetHeaderValue::byName(
            $secondResponse,
            'X-Rate-Limit-Policy',
        ));
        static::assertSame('client', GetHeaderValue::byName(
            $secondResponse,
            'X-Rate-Limit-Rules',
        ));
        static::assertSame('1:5:10', GetHeaderValue::byName(
            $secondResponse,
            'X-Rate-Limit-Client',
        ));
        static::assertSame('2:5:10', GetHeaderValue::byName(
            $secondResponse,
            'X-Rate-Limit-Client-State',
        ));
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws PHPUnitException
     */
    #[Test]
    public function rateLimitHeadersOmittedOn401(): void
    {
        $response = self::stubRouter(
            handler: new StubHandler(),
            authenticator: new StubAuthenticator(null),
        )
            ->dispatch(self::buildStubRequest('GET', '/character/1'));

        static::assertSame(401, $response->getStatusCode());

        $headers = $response->getHeaders();
        foreach ([
            'X-Rate-Limit-Policy',
            'X-Rate-Limit-Rules',
            'X-Rate-Limit-Client',
            'X-Rate-Limit-Client-State',
        ] as $name) {
            static::assertFalse(
                $headers->has($name),
                "{$name} should not be set on a 401",
            );
        }
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws PHPUnitException
     */
    #[Test]
    public function rateLimitHeadersOmittedOn403(): void
    {
        $response = self::stubRouter(
            handler: new StubHandler(),
            authenticator: new StubAuthenticator(self::stubToken([])),
        )
            ->dispatch(self::buildStubRequest('GET', '/character/1'));

        static::assertSame(403, $response->getStatusCode());

        $headers = $response->getHeaders();
        foreach ([
            'X-Rate-Limit-Policy',
            'X-Rate-Limit-Rules',
            'X-Rate-Limit-Client',
            'X-Rate-Limit-Client-State',
        ] as $name) {
            static::assertFalse(
                $headers->has($name),
                "{$name} should not be set on a 403",
            );
        }
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws PHPUnitException
     */
    #[Test]
    public function unknownPathIsNotFound(): void
    {
        $response = self::stubRouter(
            new StubHandler(),
            new StubAuthenticator(self::stubToken()),
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
            new StubHandler(),
            new StubAuthenticator(self::stubToken()),
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
            new StubHandler(),
            new StubAuthenticator(self::stubToken()),
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
            new StubHandler(),
            new StubAuthenticator(self::stubToken()),
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
        $handler = new StubHandler();
        $response = self::stubRouter(
            $handler,
            new StubAuthenticator(self::stubToken()),
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
            new StubHandler(),
            new StubAuthenticator(null),
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
            new StubHandler(),
            new StubAuthenticator(self::stubToken(['other:scope'])),
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
            new StubHandler(),
            new StubAuthenticator(self::stubToken()),
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
        $handler = new StubHandler();
        $token = self::stubToken();

        self::stubRouter($handler, new StubAuthenticator($token))
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
            new StubHandler(),
            new StubAuthenticator(null),
        )
            ->dispatch(self::buildStubRequest('DELETE', '/character/1'));

        static::assertSame(405, $response->getStatusCode());
    }
}
