<?php

declare(strict_types=1);

namespace Thesis\Protobuf\Registry;

/**
 * @api
 */
final class Pool
{
    private static self $instance;

    public static function get(): self
    {
        return self::$instance ??= new self();
    }

    /** @var array<non-empty-string, Descriptor> */
    private array $descriptors = [];

    /** @var array<non-empty-string, File> */
    private array $files = [];

    /** @var array<non-empty-string, File\MessageDescriptor> map File\MessageDescriptor by typename, used by google.protobuf.Any */
    private array $messages = [];

    /** @var array<non-empty-string, File\EnumDescriptor> map File\EnumDescriptor by typename */
    private array $enums = [];

    /** @var array<non-empty-string, File\ServiceDescriptor> map File\ServiceDescriptor by typename, used by server reflection */
    private array $services = [];

    /** @var array<class-string, non-empty-string> map typename by fqcn, used by google.protobuf.Any */
    private array $types = [];

    /** @var array<non-empty-string, non-empty-string> map filename by typename, used by server reflection */
    private array $symbols = [];

    /**
     * @param non-empty-string $type
     */
    public function messageDescriptorByType(string $type): ?File\MessageDescriptor
    {
        return $this->messages[$type] ?? null;
    }

    /**
     * @param class-string $fqcn
     * @return ?non-empty-string
     */
    public function classType(string $fqcn): ?string
    {
        return $this->types[$fqcn] ?? null;
    }

    /**
     * @param non-empty-string $type
     */
    public function enumDescriptorByType(string $type): ?File\EnumDescriptor
    {
        return $this->enums[$type] ?? null;
    }

    /**
     * @param class-string $fqcn
     * @return ?non-empty-string
     */
    public function enumType(string $fqcn): ?string
    {
        return $this->types[$fqcn] ?? null;
    }

    /**
     * @param non-empty-string $type
     */
    public function serviceDescriptorByType(string $type): ?File\ServiceDescriptor
    {
        return $this->services[$type] ?? null;
    }

    /**
     * @param non-empty-string $filename
     */
    public function fileByName(string $filename): ?File
    {
        return $this->files[$filename] ?? null;
    }

    /**
     * @param non-empty-string $symbol
     */
    public function fileBySymbol(string $symbol): ?File
    {
        if (!isset($this->symbols[$symbol])) {
            return null;
        }

        return $this->files[$this->symbols[$symbol]] ?? null;
    }

    /**
     * @param non-empty-string $filename
     */
    public function descriptorByFilename(string $filename): ?Descriptor
    {
        return $this->descriptors[$filename] ?? null;
    }

    public function register(Registrar ...$registries): self
    {
        $pool = self::get();

        foreach ($registries as $registry) {
            $registry->register($pool);
        }

        return $pool;
    }

    public function add(Descriptor $descriptor, File $file): self
    {
        $pool = self::get();

        $pool->descriptors[$file->name] = $descriptor;
        $pool->files[$file->name] = $file;

        foreach ($file->messages as $message) {
            if (isset($pool->messages[$message->name])) {
                self::throwTypeAlreadyRegistered($message->name);
            }

            $pool->messages[$message->name] = $message;
            $pool->symbols[$message->name] = $file->name;
            $pool->types[$message->fqcn] = $message->name;
        }

        foreach ($file->enums as $enum) {
            if (isset($pool->enums[$enum->name])) {
                self::throwTypeAlreadyRegistered($enum->name);
            }

            $pool->enums[$enum->name] = $enum;
            $pool->symbols[$enum->name] = $file->name;
            $pool->types[$enum->fqcn] = $enum->name;
        }

        foreach ($file->services as $service) {
            if (isset($pool->services[$service->name])) {
                self::throwTypeAlreadyRegistered($service->name);
            }

            $pool->services[$service->name] = $service;

            foreach ($service->methods as $method) {
                $pool->symbols["{$service->name}.{$method->name}"] = $file->name;
            }
        }

        return $pool;
    }

    /**
     * @param non-empty-string $type
     */
    private static function throwTypeAlreadyRegistered(string $type): never
    {
        throw new \RuntimeException(\sprintf('Type "%s" is already registered in the \Thesis\Protobuf\Registry\Pool. Ensure that you are using protobuf compiler correctly, or use \Thesis\Protobuf\Registry\OnceRegistrar to prevent duplicate registration of types in the pool', $type));
    }

    private function __construct() {}
}
