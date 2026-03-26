<?php

declare(strict_types=1);

namespace Thesis\Protobuf\Registry;

/**
 * @api
 */
final readonly class OnceRegistrar implements Registrar
{
    public function __construct(
        private Registrar $registrar,
    ) {}

    #[\Override]
    public function register(Pool $pool): void
    {
        /** @var array<class-string<Registrar>, true> $registered */
        static $registered = [];

        $fqcn = $this->registrar::class;

        if (!isset($registered[$fqcn])) {
            $pool->register($this->registrar);
            $registered[$fqcn] = true;
        }
    }
}
