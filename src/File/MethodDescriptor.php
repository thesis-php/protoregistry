<?php

declare(strict_types=1);

namespace Thesis\Protobuf\Registry\File;

/**
 * @api
 */
final readonly class MethodDescriptor
{
    /**
     * @param non-empty-string $name
     */
    public function __construct(
        public string $name,
        public bool $clientStream = false,
        public bool $serverStream = false,
    ) {}
}
