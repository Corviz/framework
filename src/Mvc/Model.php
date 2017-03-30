<?php

namespace Corviz\Mvc;

use Corviz\Database\Connection;
use Corviz\Database\Query;
use Corviz\Database\Query\WhereClause;

class Model
{
    /**
     * @var string
     */
    protected static $connection;

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
     * @return Query
     */
    public static function find() : Query
    {
        $query = self::$connectionObject->createQuery()
            ->from(self::$table);

        return $query;
    }

    /**
     * @param int|string|array $primary
     *
     * @return static
     * @throws \Exception
     */
    public static function load($primary)
    {
        $object = new static();

        //Check for non-arrays passed in $primary
        if (count(self::$primaryKey) == 1) {

            if (!is_array($primary)) {
                $primary = [
                    static::$primaryKey[0] => $primary
                ];
            }

        }

        //Has differences between parameter keys and primary keys indexes
        $pkDiffs = array_diff(array_keys($primary), self::$primaryKey);
        if (!empty($pkDiffs)) {
            throw new \Exception('Wrong primary key format');
        }

        $query = self::$connectionObject->createQuery();
        $result = $query->from(self::$table)
            ->where(function(WhereClause $whereClause) use ($primary){
                foreach ($primary as $key => $value) {
                    $whereClause->and($key, '=', '?');
                }
            })->execute(array_values($primary));

        if ($result->count()) {
            $rowData = $result->fetch()->getData();

            foreach (self::$dates as $dateField) {
                $rowData[$dateField] = date_parse_from_format(
                    self::$connectionObject->getDateFormat(),
                    $rowData[$dateField]
                );
            }

            $object->fill($rowData);
        }

        return $object;
    }

    /**
     *
     */
    private static function initAttibutes()
    {
        //stop
        if (self::$initializedAttrs) {
            return;
        }

        //Add timestamps
        if (self::$timestamps) {
            self::$dates[] = 'createdAt';
            self::$dates[] = 'updatedAt';
            self::$dates[] = 'deletedAt';
        }

        //Standardize primaryKey attribute
        self::$primaryKey = (array) self::$primaryKey;
        if (empty(self::$primaryKey)) {
            throw new \Exception("Could not proceed: Object has no primary keys.");
        }
    }

    /**
     * @param array $data
     */
    final public function fill(array $data) {
        if (empty($data)) {
            return;
        }

        $filtered = array_intersect_key(
            $data,
            array_flip(self::$fields)
        );

        $this->data = array_replace($this->data, $filtered);
    }

    /**
     * @return array
     */
    final public function getPrimaryKeys() : array
    {
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
     */
    public function save(array $data = [])
    {
        $this->fill($data);
        self::$connectionObject->save($this);
    }

    /**
     * Model constructor.
     *
     * @param array $data
     */
    final public function __construct(array $data = [])
    {
        self::initAttibutes();
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
}
