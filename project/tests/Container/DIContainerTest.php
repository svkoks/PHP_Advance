<?php

namespace GeekBrains\Blog\UnitTests\Container;

use SomeClass;
use PHPUnit\Framework\TestCase;
use GeekBrains\Project\Blog\Container\DIContainer;
use GeekBrains\Project\Blog\Exceptions\NotFoundException;
use GeekBrains\Project\tests\Container\SomeClassWithParameter;
use GeekBrains\Project\tests\Container\ClassDependingOnAnother;
use GeekBrains\Project\Blog\Repositories\UsersReposirory\InMemoryUsersRepository;
use GeekBrains\Project\Blog\Repositories\UsersRepository\UsersRepositoryInterface;

class DIContainerTest extends TestCase
{

    public function testItResolvesClassWithDependencies(): void
    {

        $container = new DIContainer();
        $container->bind(
            SomeClassWithParameter::class,
            new SomeClassWithParameter(42)
        );

        $object = $container->get(ClassDependingOnAnother::class);
        $this->assertInstanceOf(
            ClassDependingOnAnother::class,
            $object
        );
    }

    public function testItReturnsPredefinedObject(): void
    {

        $container = new DIContainer();
        $container->bind(
            SomeClassWithParameter::class,
            new SomeClassWithParameter(42)
        );

        $object = $container->get(SomeClassWithParameter::class);
        $this->assertInstanceOf(
            SomeClassWithParameter::class,
            $object
        );

        $this->assertSame(42, $object->value());
    }

    public function testItResolvesClassByContract(): void
    {
        // Создаём объект контейнера
        $container = new DIContainer();

        // Устанавливаем правило, по которому
        // всякий раз, когда контейнеру нужно
        // создать объект, реализующий контракт
        // UsersRepositoryInterface, он возвращал бы
        // объект класса InMemoryUsersRepository
        $container->bind(
            UsersRepositoryInterface::class,
            InMemoryUsersRepository::class
        );

        // Пытаемся получить объект класса,
        // реализующего контракт UsersRepositoryInterface
        $object = $container->get(UsersRepositoryInterface::class);

        // Проверяем, что контейнер вернул
        // объект класса InMemoryUsersRepository
        $this->assertInstanceOf(
            InMemoryUsersRepository::class,
            $object
        );
    }


    public function testItResolvesClassWithoutDependencies(): void
    {
        $container = new DIContainer();
        $object = $container->get(SomeClassWithoutDependencies::class);


        $this->assertInstanceOf(
            SomeClassWithoutDependencies::class,
            $object
        );
    }


    public function testItThrowsAnExceptionIfCannotResolveType(): void
    {
        // Создаём объект контейнера
        $container = new DIContainer();
        // Описываем ожидаемое исключение
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(
            'Cannot resolve type: GeekBrains\Blog\UnitTests\Container\SomeClass'
        );
        // Пытаемся получить объект несуществующего класса
        $container->get(SomeClass::class);
    }
}
