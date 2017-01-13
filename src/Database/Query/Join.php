<?php

namespace Corviz\Database\Query;

class Join
{
    const TYPE_INNER = 'inner';
    const TYPE_LEFT = 'left';
    const TYPE_RIGHT = 'right';

    /**
     * @var string
     */
    private $table;

    /**
     * @var string
     */
    private $type;

    /**
     * @var WhereClause
     */
    private $whereClause;

    /**
     * Switches type to INNER JOIN.
     */
    public function inner()
    {
        $this->type = self::TYPE_INNER;
    }

    /**
     * Switches type to LEFT JOIN.
     */
    public function left()
    {
        $this->type = self::TYPE_LEFT;
    }

    /**
     * Switches type to RIGHT JOIN.
     */
    public function right()
    {
        $this->type = self::TYPE_RIGHT;
    }

    /**
     * Join constructor.
     *
     * @param string $table
     * @param string $type
     */
    public function __construct(
        string $table,
        string $type = self::TYPE_LEFT
    ) {
        $this->type = $type;
        $this->table = $table;
        $this->whereClause = new WhereClause();
    }
}
