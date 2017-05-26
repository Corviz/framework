<?php

namespace Corviz\Database;

use Corviz\Application;

class ConnectionFactory
{
    /**
     * @var \Corviz\Database\Connection[]
     */
    private static $connections = [];

    /**
     * Creates a connection instance by its name.
     *
     * @param string|null $connectionName
     *
     * @throws \Exception
     *
     * @return \Corviz\Database\Connection
     */
    public static function build(string $connectionName = null) : Connection
    {
        $configs = Application::current()->config('database');
        $connectionName = $connectionName ?: $configs['default'];

        if (!isset($configs[$connectionName])) {
            throw new \Exception("Unknown database connection: '$connectionName'");
        }

        if (!isset(self::$connections[$connectionName])) {
            $connectionClass = $configs[$connectionName]['driver'];
            $options = $configs[$connectionName]['options'];
            self::$connections[$connectionName] = new $connectionClass($options);
        }

        return self::$connections[$connectionName];
    }
}
