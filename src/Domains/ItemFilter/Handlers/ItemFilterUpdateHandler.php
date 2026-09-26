<?php

declare(strict_types=1);

namespace LaminasApiSample\Domains\ItemFilter\Handlers;

use Laminas\Http\PhpEnvironment\Request as HttpRequest;
use Laminas\Http\PhpEnvironment\Response as HttpResponse;
use Laminas\InputFilter\Exception\RuntimeException;
use LaminasApiSample\Domains\ItemFilter\ItemFilterAdapter;
use LaminasApiSample\Domains\ItemFilter\ItemFilterRepository;
use LaminasApiSample\Domains\ItemFilter\Validation\ItemFilterUpdateValidator;
use LaminasApiSample\Http\Exceptions\MalformedJsonException;
use LaminasApiSample\Http\Exceptions\UnsupportedContentTypeException;
use LaminasApiSample\Http\HandlerInterface;
use LaminasApiSample\Http\JsonBody;
use LaminasApiSample\Http\JsonResponseFactory;
use Override;
use Psr\Clock\ClockInterface;

final class ItemFilterUpdateHandler implements HandlerInterface
{
    public function __construct(
        private readonly ItemFilterRepository $repository,
        private readonly ItemFilterAdapter $adapter,
        private readonly ItemFilterUpdateValidator $validation,
        private readonly ClockInterface $clock,
    ) {}

    /**
     * @param array<string, string> $params
     * @throws RuntimeException
     */
    #[Override]
    public function __invoke(HttpRequest $request, array $params): HttpResponse
    {
        $id = $params['id'] ?? null;
        if (!$id) {
            return JsonResponseFactory::badRequest();
        }

        $entity = $this->repository->find($id);
        if (!$entity) {
            return JsonResponseFactory::notFound();
        }

        try {
            $data = JsonBody::parse($request);

            $this->validation->setData($data);
            if (!$this->validation->isValid()) {
                return JsonResponseFactory::unprocessable(
                    $this->getFirstValidationMessage(),
                );
            }
        } catch (UnsupportedContentTypeException) {
            return JsonResponseFactory::unsupportedContentType();
        } catch (MalformedJsonException) {
            return JsonResponseFactory::badRequest();
        }

        $now = $this->clock->now();
        $entity->update($this->validation->getPatch(), $now);
        if ($this->validation->isPublishRequested()) {
            $entity->publish($now);
        }

        $this->repository->save($entity);

        return JsonResponseFactory::ok([
            'filter' => $this->adapter->mapResponse($entity),
        ]);
    }

    private function getFirstValidationMessage(): string
    {
        foreach ($this->validation->getMessages() as $field => $messages) {
            $first = reset($messages);
            if (is_string($first)) {
                return "{$field}: {$first}";
            }
        }

        return 'Invalid input';
    }
}
