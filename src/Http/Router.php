<?php

declare(strict_types=1);

namespace LaminasApiSample\Http;

use Laminas\Http\PhpEnvironment\Request as HttpRequest;
use Laminas\Http\PhpEnvironment\Response as HttpResponse;
use Laminas\Router\Http\TreeRouteStack;
use Laminas\Router\RouteMatch;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\ServiceManager\ServiceManager;
use LaminasApiSample\Domains\Auth\AccessTokenEntity;
use LaminasApiSample\Http\Auth\AuthContext;
use LaminasApiSample\Http\RateLimiting\RateLimiter;
use LaminasApiSample\Http\RateLimiting\RateLimitHeaders;
use Psr\Container\ContainerExceptionInterface;

final class Router
{
    public function __construct(
        private TreeRouteStack $routeStack,
        private ServiceManager $serviceManager,
        private Auth\AuthenticatorInterface $authenticator,
        private RateLimiter $rateLimiter,
    ) {}

    /** @throws ContainerExceptionInterface */
    public function dispatch(HttpRequest $request): HttpResponse
    {
        $route = $this->routeStack->match($request);
        if ($route === null) {
            return JsonResponseFactory::notFound();
        }

        $serviceName = $this->handlerName($route, $request);
        if ($serviceName === null) {
            return JsonResponseFactory::methodNotAllowed($this->allowedMethods(
                $route,
            ));
        }

        $scope = $this->requiredScope($route);
        if ($scope === null) {
            return JsonResponseFactory::serverError();
        }

        $token = $this->authenticate($request);
        if ($token === null) {
            return JsonResponseFactory::unauthorized();
        }

        if (!$token->hasScope($scope)) {
            return JsonResponseFactory::forbidden($scope);
        }

        $limit = $this->rateLimiter->check('client:' . $token->getId());
        if ($limit->isLimited()) {
            return JsonResponseFactory::rateLimited($limit);
        }

        $handler = $this->loadHandler($serviceName);
        if ($handler === null) {
            return JsonResponseFactory::notImplemented();
        }

        $response = $handler(
            $request,
            $this->routeParams($route),
            $this->authContext($token),
        );

        return RateLimitHeaders::apply($response, $limit);
    }

    private function handlerName(
        RouteMatch $route,
        HttpRequest $request,
    ): ?string {
        /** @var array<string, string> $handlers */
        $handlers = $route->getParam('handlers', []);
        return $handlers[$request->getMethod()] ?? null;
    }

    /** @return list<string> */
    private function allowedMethods(RouteMatch $route): array
    {
        /** @var array<string, string> $handlers */
        $handlers = $route->getParam('handlers', []);
        return array_keys($handlers);
    }

    private function requiredScope(RouteMatch $route): ?string
    {
        // @mago-expect analysis:mixed-assignment
        $scope = $route->getParam('scope');
        return is_string($scope) ? $scope : null;
    }

    private function authenticate(HttpRequest $request): ?AccessTokenEntity
    {
        try {
            return $this->authenticator->authenticate($request);
        } catch (Auth\AuthenticationFailedException) {
            return null;
        }
    }

    /** @throws ContainerExceptionInterface */
    private function loadHandler(string $serviceName): ?HandlerInterface
    {
        try {
            /** @var HandlerInterface */
            return $this->serviceManager->get($serviceName);
        } catch (ServiceNotFoundException $exception) {
            $detail = implode(' - ', [
                $exception::class,
                $exception->getMessage(),
            ]);
            error_log("Error: \n{$detail}\n");
            return null;
        }
    }

    /** @return array<string, string> */
    private function routeParams(RouteMatch $route): array
    {
        /** @var array<string, string> */
        return array_diff_key($route->getParams(), [
            'handlers' => true,
            'scope' => true,
        ]);
    }

    // @mago-expect lint:sensitive-parameter
    private function authContext(AccessTokenEntity $token): AuthContext
    {
        return new AuthContext($token->getProfile(), $token->getScopes());
    }
}
