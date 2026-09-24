<?php

declare(strict_types=1);

namespace LaminasApiSample\Http;

use JsonException;
use Laminas\Http\Header\ContentType;
use Laminas\Http\PhpEnvironment\Request as HttpRequest;
use LaminasApiSample\Http\Exceptions\MalformedJsonException;
use LaminasApiSample\Http\Exceptions\UnsupportedContentTypeException;

final class JsonBody
{
    /**
     * @return array<string, mixed>
     * @throws UnsupportedContentTypeException
     * @throws MalformedJsonException
     */
    public static function parse(HttpRequest $request): array
    {
        $header = $request->getHeader('Content-Type');
        if (
            !$header instanceof ContentType
            || $header->getMediaType() !== 'application/json'
        ) {
            throw new UnsupportedContentTypeException();
        }

        try {
            // @mago-expect analysis:mixed-assignment
            $data = json_decode(
                $request->getContent(),
                true,
                flags: JSON_THROW_ON_ERROR,
            );
        } catch (JsonException $e) {
            throw new MalformedJsonException($e->getMessage(), previous: $e);
        }

        if (!is_array($data) || array_is_list($data)) {
            throw new MalformedJsonException('Expected a JSON object.');
        }

        /** @var array<string, mixed> $data */
        return $data;
    }
}
