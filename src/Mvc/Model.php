<?php

namespace Corviz\Mvc;

use Corviz\Database\Connection;
use Corviz\Database\ConnectionFactory;
use Corviz\Database\Query\WhereClause;

class Model
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
    private static $connectionObject;

    /**
     * @var bool
     */
    private static $initializedAttrs = false;

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

        $query = self::$connectionObject->createQuery();
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

            $object->fill($rowData);
        }

        return $object;
    }

    private static function initAttibutes()
    {
        //stop
        if (self::$initializedAttrs) {
            return;
        }

        //Add timestamps
        if (static::$timestamps) {
            static::$dates[] = 'createdAt';
            static::$dates[] = 'updatedAt';
            static::$dates[] = 'deletedAt';
        }

        //Initialize database connection
        if (!self::$connectionObject) {
            self::$connectionObject = ConnectionFactory::build(self::$connection);
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
     */
    final public function fill(array $data)
    {
        if (empty($data)) {
            return;
        }

        $filtered = array_intersect_key(
            $data,
            array_flip(static::$fields)
        );

        $this->data = array_replace($this->data, $filtered);
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

        return self::$primaryKey;
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
        return self::$table;
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
