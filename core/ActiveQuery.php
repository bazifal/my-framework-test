<?php
/**
 * Created by PhpStorm.
 * User: bazifal
 * Date: 14.11.2017
 * Time: 16:01
 */

namespace core;

/**
 * Class ActiveQuery
 * @package core
 */
class ActiveQuery extends Component
{
    /**
     * @var string
     */
    public $class;

    /**
     * @var array
     */
    protected $select = [];

    /**
     * @var array
     */
    protected $where = [];

    /**
     * @var array
     */
    protected $join = [];

    /**
     * @var int
     */
    protected $limit;

    /**
     * @var int
     */
    protected $offset;

    /**
     * @var array
     */
    protected $order = [];

    /**
     * @var array
     */
    protected $params = [];

    /**
     * @param array $select
     * @return ActiveQuery
     */
    public function select(array $select): ActiveQuery
    {
        $this->select = $select;
        return $this;
    }

    /**
     * @param $cond
     * @param array $params
     * @return ActiveQuery
     */
    public function where($cond, $params = []): ActiveQuery
    {
        if (is_array($cond)) {
            foreach ($cond as $k => $v) {
                $this->where[] = "$k = $v";
            }
        } else {
            $this->where[] = $cond;
        }
        $this->params = array_merge($this->params, $params);
        return $this;
    }

    /**
     * @param $table
     * @param $on
     * @param $type
     * @return $this
     */
    public function join($table, $on, $type = 'INNER'): ActiveQuery
    {
        $this->join[] = [
            'table' => $table,
            'type' => $type,
            'on' => $on
        ];
        return $this;
    }

    /**
     * @param $limit
     * @return ActiveQuery
     */
    public function limit($limit): ActiveQuery
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @param $offset
     * @return ActiveQuery
     */
    public function offset($offset): ActiveQuery
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * @param $order
     * @return ActiveQuery
     */
    public function orderBy($order): ActiveQuery
    {
        $this->order = $order;
        return $this;
    }

    /**
     * @return string
     */
    protected function buildQuery(): string
    {
        /** @var ActiveRecord $class */
        $class = $this->class;
        $sql = 'SELECT ';
        if (empty($this->select)) {
            $sql .= '*';
        } else {
            $sql .= implode(',', $this->select);
        }
        $sql .= ' FROM ' . $class::tableName();
        if (!empty($this->join)) {
            foreach ($this->join as $join) {
                $sql .= $join['type'] . ' JOIN ' . $join['table'] . ' ON ' . $join['on'];
            }
        }

        if (!empty($this->where)) {
            $sql .= ' WHERE ' . implode(' AND ', $this->where);
        }

        if (!empty($this->order)) {
            $sql .= ' ORDER BY ' . $this->order;
        }

        if (!empty($this->limit) && !empty($this->offset)) {
            $sql .= ' LIMIT ' . $this->offset . ',' . $this->limit;
        }

        if (!empty($this->limit)) {
            $sql .= ' LIMIT ' .  $this->limit;
        }

        if (!empty($this->offset)) {
            $sql .= ' LIMIT ' .  $this->offset . ', 1';
        }

        return $sql;
    }

    /**
     * @param $attributes
     * @return ActiveRecord
     */
    protected function createModel($attributes): ActiveRecord
    {
        return new $this->class($attributes);
    }

    /**
     * находит одну модель
     * @return ActiveRecord|null
     */
    public function one(): ActiveRecord
    {
        $this->limit(1);
        $result = Base::$app->db->query($this->buildQuery(), $this->params);
        if (!empty($result)) {
            return $this->createModel($result[0]);
        }
        return null;
    }

    /**
     * находит несколько моделей
     * @return ActiveRecord[]
     */
    public function all(): array
    {
        $queryResult = Base::$app->db->query($this->buildQuery(), $this->params);
        if (empty($queryResult)) {
            return [];
        }
        $result = [];
        foreach ($queryResult as $row) {
            $result[] = $this->createModel($row);
        }
        return $result;
    }
}