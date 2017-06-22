<?php

namespace Corviz\Mvc;

use Corviz\Database\Connection;
use Corviz\Database\ConnectionFactory;
use Corviz\Database\Query\WhereClause;

abstract class Model
{
    /**
     * @var string
     */
    protected static $connection = null;

    /**
     * @var array
     */
    protected static $dates = [];

    /**
     * @var string[]
     */
    protected static $fields = [];

    /**
     * @var string|string[]
     */
    protected static $primaryKey;

    /**
     * @var string
     */
    protected static $table;

    /**
     * @var bool
     */
    protected static $timestamps = false;

    /**
     * @var Connection
     */
    protected static $connectionObject;

    /**
     * @var bool
     */
    protected static $initializedAttrs = false;

    /**
     * @var array
     */
    private $data = [];

    /**
     * @param \Closure|null $filterFn An anonymous function that receives
     *                                an instance of \Corviz\Database\Query as parameter
     *
     * @return array
     */
    public static function find(\Closure $filterFn = null) : array
    {
        $query = static::$connectionObject->createQuery()
            ->from(static::$table);

        //Filter results
        if (!is_null($filterFn)) {
            $filterFn($query);
        }

        $objList = [];
        $searchKeys = static::getPrimaryKeys();
        foreach ($searchKeys as &$key) {
            $key = static::$table.'.'.$key;
        }

        $query->select(...$searchKeys);
        $result = $query->execute();

        if ($result->count()) {
            while ($row = $result->fetch()) {
                $objList[] = static::load($row->getData());
            }
        }

        return $objList;
    }

    /**
     * @param int|string|array $primary
     *
     * @throws \Exception
     *
     * @return static
     */
    public static function load($primary)
    {
        $primary = static::normalizePrimaryKeys($primary);
        $object = new static();

        $query = static::$connectionObject->createQuery();
        $result = $query->from(static::$table)
            ->where(function (WhereClause $whereClause) use ($primary) {
                foreach ($primary as $key => $value) {
                    $whereClause->and($key, '=', '?');
                }
            })->execute(array_values($primary));

        if ($result->count()) {
            $rowData = $result->fetch()->getData();

            foreach (static::$dates as $dateField) {
                $rowData[$dateField] = date_parse_from_format(
                    static::$connectionObject->getDateFormat(),
                    $rowData[$dateField]
                );
            }

            $object->fill($rowData, false);
        }

        return $object;
    }

    private static function initAttibutes()
    {
        //stop
        if (static::$initializedAttrs) {
            return;
        }

        //Add timestamps
        if (static::$timestamps) {
            static::$dates[] = 'created_at';
            static::$dates[] = 'updated_at';
        }

        //Initialize database connection
        if (!static::$connectionObject) {
            static::$connectionObject = ConnectionFactory::build(static::$connection);
        }
    }

    /**
     * @param $primary
     *
     * @throws \Exception
     *
     * @return array
     */
    private static function normalizePrimaryKeys($primary) : array
    {
        $pks = static::getPrimaryKeys();

        if (!is_array($primary) && count($pks) == 1) {
            $primary = [$pks[0] => $primary];
        } elseif (array_diff(array_keys($primary), $pks)) {
            throw new \Exception('Invalid primary key.');
        }

        return $primary;
    }

    /**
     * @param array $data
     * @param bool  $applySetters
     */
    final public function fill(array $data, $applySetters = true)
    {
        if (empty($data)) {
            return;
        }

        $filtered = array_intersect_key(
            $data,
            array_flip(static::$fields)
        );

        if (empty($filtered)) {
            return;
        }

        if ($applySetters) {
            foreach ($filtered as $field => $value) {
                $this->$field = $value;
            }
        } else {
            $this->data = array_replace($this->data, $filtered);
        }
    }

    /**
     * @throws \Exception
     *
     * @return array
     */
    final public function getPrimaryKeys() : array
    {
        if (!is_array(static::$primaryKey)) {
            static::$primaryKey = (array) static::$primaryKey;

            if (empty(static::$primaryKey)) {
                throw new \Exception('Primary key attribute can\'t be empty');
            }
        }

        return static::$primaryKey;
    }

    /**
     * @return array
     */
    final public function getData() : array
    {
        return $this->data;
    }

    /**
     * @return string
     */
    final public function getTable() : string
    {
        return static::$table;
    }

    /**
     * @param array $data
     *
     * @return bool
     */
    public function save(array $data = []) : bool
    {
        $this->fill($data);
        $result = static::$connectionObject->save($this);

        return $result->count() > 0;
    }

    /**
     * Apply getters/setters for specific fields.
     *
     * @param string $fieldName
     * @param $value
     * @param $methodPrefix
     */
    private function applyModifier(string $fieldName, &$value, $methodPrefix)
    {
        $methodName = $methodPrefix.$this->fieldNameToUpperCamelCase($fieldName);
        if (method_exists($this, $methodName)) {
            $value = $this->$methodName($value);
        }
    }

    /**
     * @param string $fieldName
     *
     * @return string
     */
    private function fieldNameToUpperCamelCase(string $fieldName) : string
    {
        $handler = function($matches) {
            return strtoupper($matches[0][1]);
        };
        $result = (string) preg_replace_callback('#\\_[a-zA-Z]#', $handler, $fieldName);
        $result = ucfirst($result);

        return $result;
    }

    /**
     * Model constructor.
     *
     * @param array $data
     */
    final public function __construct(array $data = [])
    {
        static::initAttibutes();
        $this->fill($data);
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    final public function &__get($name)
    {
        $value = $this->data[$name];

        //Apply accessor
        $this->applyModifier($name, $value, 'get');

        return $value;
    }

    /**
     * @param $name
     *
     * @return bool
     */
    final public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    /**
     * @param $name
     * @param $value
     *
     * @throws \Exception
     */
    final public function __set($name, $value)
    {
        //is date?
        if (
            in_array($name, $this->dates)
            && !($value instanceof \DateTime)
        ) {
            throw new \Exception("Field '$name' should be a DateTime instance.");
        }

        //Apply modifier
        $this->applyModifier($name, $value, 'set');

        $this->data[$name] = $value;
    }

    /**
     * @param $name
     */
    final public function __unset($name)
    {
        unset($this->data[$name]);
    }
}
