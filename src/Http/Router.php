<?php

declare(strict_types=1);

namespace LaminasApiSample\Http;

use Laminas\Http\PhpEnvironment\Request as HttpRequest;
use Laminas\Http\PhpEnvironment\Response as HttpResponse;
use Laminas\Router\Http\TreeRouteStack;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\ServiceManager\ServiceManager;
use Psr\Container\ContainerExceptionInterface;

final class Router
{
    public function __construct(
        private TreeRouteStack $routeStack,
        private ServiceManager $serviceManager,
        private Auth\AuthenticatorInterface $authenticator,
    ) {}

    /** @throws ContainerExceptionInterface */
    public function dispatch(HttpRequest $request): HttpResponse
    {
        $routeMatch = $this->routeStack->match($request);
        if ($routeMatch === null) {
            return JsonResponseFactory::notFound();
        }

        $allParams = $routeMatch->getParams();

        /** @var array<string, string> $handlers */
        $handlers = $allParams['handlers'] ?? [];

        $serviceName = $handlers[$request->getMethod()] ?? null;
        if ($serviceName === null) {
            return JsonResponseFactory::methodNotAllowed(array_keys($handlers));
        }

        /** @var array<string, string> $params */
        $params = array_diff_key($allParams, [
            'handlers' => true,
            'scope' => true,
        ]);

        /** @var null|string $scope */
        $scope = $allParams['scope'] ?? null;
        if (!is_string($scope)) {
            return JsonResponseFactory::serverError();
        }

        try {
            $token = $this->authenticator->authenticate($request);
        } catch (Auth\AuthenticationFailedException) {
            return JsonResponseFactory::unauthorized();
        }

        if (!$token->hasScope($scope)) {
            return JsonResponseFactory::forbidden($scope);
        }

        try {
            /** @var HandlerInterface $handler */
            $handler = $this->serviceManager->get($serviceName);
        } catch (ServiceNotFoundException $e) {
            $detail = implode(' - ', [$e::class, $e->getMessage()]);
            error_log("Error: \n{$detail}\n");
            return JsonResponseFactory::notImplemented();
        }

        return $handler(
            $request,
            $params,
            new Auth\AuthContext($token->getProfile(), $token->getScopes()),
        );
    }
}
