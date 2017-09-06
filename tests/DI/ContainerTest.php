<?php

namespace Tests\Corviz\Framework\DI;

use Corviz\DI\Container;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    public function testCallToDeclaredDependency()
    {
        $container = new Container();
        $container->set('DB', MySql::class);

        $this->assertTrue($container->get('DB') instanceof MySql);
    }

    public function testCallToUndeclaredShouldThrowException()
    {
        $this->expectException(\Exception::class);
        $container = new Container();
        $container->get('DoesNotExist')->doStuff();
    }

    public function testChangeContainerDependencyInRuntime()
    {
        $container = new Container();

        $container->set('DB', MySql::class);
        $this->assertTrue($container->get('DB') instanceof MySql, 'Expecting MySql instance');

        $container->set('DB', Postgres::class);
        $this->assertTrue($container->get('DB') instanceof Postgres, 'Expecting Postgres instance');
    }
}

/*
 * Stub classes used only for container test
 */

interface Database
{
    public function select(string $columns, string $table);
}

class MySql implements Database
{
    public function select(string $columns, string $table)
    {
        return 'mysqlSelect';
    }
}

class Postgres implements Database
{
    public function select(string $columns, string $table)
    {
        return 'postgresSelect';
    }
}

class Model
{
    private $database;

    /**
     * @return Database
     */
    public function getDatabase(): Database
    {
        return $this->database;
    }

    public function __construct(Database $database)
    {
        $this->database = $database;
    }
}
