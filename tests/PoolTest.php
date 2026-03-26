<?php

declare(strict_types=1);

namespace Thesis\Protobuf\Registry;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Pool::class)]
final class PoolTest extends TestCase
{
    public function testMessageRegistered(): void
    {
        $pool = Pool::get();
        $pool->add($descriptor = Descriptor::raw('xyz'), $file = new File(
            name: 'api.proto',
            messages: [
                $md = new File\MessageDescriptor(
                    name: 'thesis.api.Request',
                    fqcn: \stdClass::class,
                ),
            ],
        ));

        self::assertEquals($md, $pool->messageDescriptorByType('thesis.api.Request'));
        self::assertEquals('thesis.api.Request', $pool->classType(\stdClass::class));
        self::assertEquals($file, $pool->fileBySymbol('thesis.api.Request'));
        self::assertEquals($descriptor, $pool->descriptorByFilename($file->name));
        $this->expectExceptionObject(new \RuntimeException('Type "thesis.api.Request" is already registered in the \Thesis\Protobuf\Registry\Pool. Ensure that you are using protobuf compiler correctly, or use \Thesis\Protobuf\Registry\OnceRegistrar to prevent duplicate registration of types in the pool'));
        $pool->add($descriptor, new File(
            name: 'api.proto',
            messages: [
                new File\MessageDescriptor(
                    name: 'thesis.api.Request',
                    fqcn: \stdClass::class,
                ),
            ],
        ));
    }

    public function testEnumRegistered(): void
    {
        $pool = Pool::get();
        $pool->add($descriptor = Descriptor::raw('xyz'), $file = new File(
            name: 'api.proto',
            enums: [
                $md = new File\EnumDescriptor(
                    name: 'thesis.api.RequestType',
                    fqcn: \stdClass::class,
                ),
            ],
        ));

        self::assertEquals($md, $pool->enumDescriptorByType('thesis.api.RequestType'));
        self::assertEquals('thesis.api.RequestType', $pool->enumType(\stdClass::class));
        self::assertEquals($file, $pool->fileBySymbol('thesis.api.RequestType'));
        self::assertEquals($descriptor, $pool->descriptorByFilename($file->name));

        $this->expectExceptionObject(new \RuntimeException('Type "thesis.api.RequestType" is already registered in the \Thesis\Protobuf\Registry\Pool. Ensure that you are using protobuf compiler correctly, or use \Thesis\Protobuf\Registry\OnceRegistrar to prevent duplicate registration of types in the pool'));
        $pool->add($descriptor, new File(
            name: 'api.proto',
            enums: [
                new File\EnumDescriptor(
                    name: 'thesis.api.RequestType',
                    fqcn: \stdClass::class,
                ),
            ],
        ));
    }

    public function testServiceRegistered(): void
    {
        $pool = Pool::get();
        $pool->add($descriptor = Descriptor::raw('xyz'), $file = new File(
            name: 'api.proto',
            services: [
                $md = new File\ServiceDescriptor(
                    name: 'thesis.api.RequestService',
                    clientFqcn: \stdClass::class,
                    methods: [
                        new File\MethodDescriptor('Echo'),
                    ],
                ),
            ],
        ));

        self::assertEquals($md, $pool->serviceDescriptorByType('thesis.api.RequestService'));
        self::assertEquals($file, $pool->fileBySymbol('thesis.api.RequestService.Echo'));
        self::assertEquals($descriptor, $pool->descriptorByFilename($file->name));

        $this->expectExceptionObject(new \RuntimeException('Type "thesis.api.RequestService" is already registered in the \Thesis\Protobuf\Registry\Pool. Ensure that you are using protobuf compiler correctly, or use \Thesis\Protobuf\Registry\OnceRegistrar to prevent duplicate registration of types in the pool'));
        $pool->add($descriptor, new File(
            name: 'api.proto',
            services: [
                new File\ServiceDescriptor(
                    name: 'thesis.api.RequestService',
                    clientFqcn: \stdClass::class,
                ),
            ],
        ));
    }

    public function testTypeNotFound(): void
    {
        self::assertNull(Pool::get()->messageDescriptorByType('thesis.api.OtherRequest'));
    }

    public function testClassNotFound(): void
    {
        self::assertNull(Pool::get()->classType(self::class));
    }
}
