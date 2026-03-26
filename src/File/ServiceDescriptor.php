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
     * @param ?class-string $clientFqcn
     * @param ?class-string $serverFqcn
     * @param list<MethodDescriptor> $methods
     */
    public function __construct(
        public string $name,
        public ?string $clientFqcn = null,
        public ?string $serverFqcn = null,
        public array $methods = [],
    ) {}
}
