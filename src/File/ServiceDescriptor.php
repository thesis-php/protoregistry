<?php

declare(strict_types=1);

namespace Thesis\Protobuf\Registry\File;

/**
 * @api
 */
final readonly class ServiceDescriptor
{
    /**
     * @param non-empty-string $name
     * @param list<MethodDescriptor> $methods
     */
    public function __construct(
        public string $name,
        public array $methods = [],
    ) {}
}
