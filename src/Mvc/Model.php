<?php

namespace Corviz\Mvc;

use Corviz\Database\Connection;
use Corviz\Database\ConnectionFactory;
use Corviz\Database\Query;
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
     * @var array
     */
    private static $modelProperties = [];

    /**
     * @var array
     */
    private $data = [];

    /**
     * @return Query
     */
    public static function createQuery() : Query
    {
        $query = self::getConnection()->createQuery();
        $query->from(self::getTable());

        return $query;
    }

    /**
     * @param \Closure|null $filterFn     An anonymous function that receives
     *                                    an instance of \Corviz\Database\Query as parameter
     * @param bool          $applySetters
     *
     * @return static[]
     */
    public static function find(\Closure $filterFn = null, bool $applySetters = false) : array
    {
        $connection = self::getConnection();
        $table = self::getModelProperty('table');
        $query = $connection->createQuery()
            ->from($table);

        //Filter results
        if (!is_null($filterFn)) {
            $filterFn($query);
        }

        $objList = [];
        $searchKeys = static::getPrimaryKeys();
        foreach ($searchKeys as &$key) {
            $key = $table.'.'.$key;
        }

        $result = $query->execute();

        if ($result->count()) {
            while ($row = $result->fetch()) {
                $instance = new static();

                $rowData = $row->getData();

                foreach (self::getDateFields() as $dateField) {
                    $rowData[$dateField] = date_create_from_format(
                        $connection->getDateFormat(),
                        $rowData[$dateField]
                    );
                }

                $instance->fill($rowData, $applySetters);

                $objList[] = $instance;
            }
        }

        return $objList;
    }

    /**
     * @return Connection
     */
    final public static function getConnection() : Connection
    {
        return self::getModelProperty('connection');
    }

    /**
     * @return array
     */
    final public static function getDateFields() : array
    {
        return self::getModelProperty('dateFields');
    }

    /**
     * @return array
     */
    final public static function getPrimaryKeys() : array
    {
        return self::getModelProperty('primaryKeys');
    }

    /**
     * @return string
     */
    final public static function getTable() : string
    {
        return self::getModelProperty('table');
    }

    /**
     * @return bool
     */
    final public static function hasTimestamps() : bool
    {
        return self::getModelProperty('timestamps');
    }

    /**
     * @param int|string|array $primary
     * @param bool             $applySetters
     *
     * @throws \Exception
     *
     * @return static|null
     */
    public static function load($primary, bool $applySetters = false)
    {
        $connection = self::getConnection();
        $primary = static::normalizePrimaryKeys($primary);
        $object = null;

        $query = $connection->createQuery();
        $result = $query->from(static::$table)
            ->where(function (WhereClause $whereClause) use ($primary) {
                foreach ($primary as $key => $value) {
                    $whereClause->and($key, '=', '?');
                }
            })->execute(array_values($primary));

        if ($result->count()) {
            $rowData = $result->fetch()->getData();

            foreach (self::getDateFields() as $dateField) {
                $rowData[$dateField] = date_create_from_format(
                    $connection->getDateFormat(),
                    $rowData[$dateField]
                );
            }

            $object = new static();
            $object->fill($rowData, $applySetters);
        }

        return $object;
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    private static function getModelProperty($name)
    {
        self::initModelProperties();
        return isset(self::$modelProperties[static::class][$name]) ?
            self::$modelProperties[static::class][$name] : null;
    }

    /**
     * Initialize static properties.
     */
    private static function initModelProperties()
    {
        $currentModel = static::class;

        if (isset(self::$modelProperties[$currentModel])) {
            return;
        }

        $properties = [
            'connection' => ConnectionFactory::build(static::$connection),
            'dateFields' => static::$dates ?: [],
            'fields' => static::$fields ?: [],
            'primaryKeys' => (array) static::$primaryKey,
            'table' => static::$table,
            'timestamps' => (bool) static::$timestamps,
        ];

        if ($properties['timestamps']) {
            $properties['fields'][] = 'created_at';
            $properties['fields'][] = 'updated_at';
            $properties['dateFields'][] = 'created_at';
            $properties['dateFields'][] = 'updated_at';
        }

        self::$modelProperties[$currentModel] = $properties;
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
        $pks = self::getPrimaryKeys();

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
            array_flip(self::getModelProperty('fields'))
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
     * @return array
     */
    final public function getData() : array
    {
        return $this->data;
    }

    /**
     * @param string $otherClass
     * @param array $fieldsMap
     * @param bool $applySetters
     *
     * @return Model[]
     */
    public function relatedToMany(
        string $otherClass,
        array $fieldsMap,
        bool $applySetters = false
    ) {
        $thisValues = array_intersect_key($this->data, $fieldsMap);

        $filterFn = function(Query $query) use (&$thisValues, &$fieldsMap){
            $query->where(function(WhereClause $where) use(&$query, &$thisValues, &$fieldsMap){
                foreach ($fieldsMap as $thisKey => $thatKey) {
                    $where->and($thatKey, '=', '?');
                    $query->bind($thisValues[$thisKey]);
                }
            });
        };

        /* @var $otherClass Model */
        return $otherClass::find($filterFn, $applySetters);
    }

    /**
     * @param string $otherClass
     * @param array $fieldsMap
     * @param bool $applySetters
     *
     * @return Model|null
     */
    public function relatedToOne(
        string $otherClass,
        array $fieldsMap,
        bool $applySetters = false
    ) {
        //@TODO remove duplicated code

        $thisValues = array_intersect_key($this->data, $fieldsMap);

        $filterFn = function(Query $query) use (&$thisValues, &$fieldsMap){
            $query->where(function(WhereClause $where) use(&$query, &$thisValues, &$fieldsMap){
                foreach ($fieldsMap as $thisKey => $thatKey) {
                    $where->and($thatKey, '=', '?');
                    $query->bind($thisValues[$thisKey]);
                }
            });

            $query->limit(1);
        };

        /* @var $otherClass Model */
        $search = $otherClass::find($filterFn, $applySetters);
        return !(empty($search)) ? $search[0] : null;
    }

    /**
     * @param array $data
     *
     * @return bool
     */
    public function save(array $data = []) : bool
    {
        $this->fill($data);
        $result = self::getConnection()->save($this);

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
        $handler = function ($matches) {
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
        static::initModelProperties();
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
            in_array($name, self::getDateFields())
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
