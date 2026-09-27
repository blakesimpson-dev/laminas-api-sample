<?php

declare(strict_types=1);

namespace LaminasApiSampleTest\Support;

use ArrayIterator;
use Laminas\Http\Header\HeaderInterface;
use Laminas\Http\PhpEnvironment\Response as HttpResponse;
use PHPUnit\Framework\Exception as PHPUnitException;
use PHPUnit\Framework\TestCase;

final class GetHeaderValue extends TestCase
{
    /** @throws PHPUnitException */
    public static function byName(HttpResponse $response, string $name): string
    {
        $header = $response->getHeaders()->get($name);
        if ($header instanceof ArrayIterator) {
            /** @var HeaderInterface|null $current */
            $current = $header->current();
            $header = $current;
        }

        if (!$header instanceof HeaderInterface) {
            static::fail("Missing {$name} header");
        }

        // Laminas' RetryAfter returns an int despite HeaderInterface declaring
        // string... :)
        // @mago-expect analysis:redundant-cast
        return (string) $header->getFieldValue();
    }
}
