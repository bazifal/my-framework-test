<?php
/**
 * Created by PhpStorm.
 * User: bazifal
 * Date: 14.11.2017
 * Time: 15:40
 */

namespace core;

/**
 * Class Model
 * @package core
 * @property int $id
 */
abstract class ActiveRecord extends Component
{
    /**
     * хранит значение сущности
     * @var array
     */
    protected $attributes = [];

    /**
     * позволяет получать поля таблицы, как свойства класса
     * @param $name
     * @return mixed|null
     * @throws \Exception
     */
    public function __get($name)
    {
        if (!static::hasAttribute($name)) {
            throw new \Exception('Поле не существует: ' . $name);
        }
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
    }

    /**
     * позволяет устанавливать значения сущности
     * @param $name
     * @param $value
     * @throws \Exception
     */
    public function __set($name, $value)
    {
        if (!static::hasAttribute($name)) {
            throw new \Exception('Поле не существует: ' . $name);
        }
        $this->attributes[$name] = $value;
    }

    /**
     * записывает в базу
     * @return bool
     */
    public function save(): bool
    {
        if (!$this->beforeSave()) {
            return false;
        }

        if ($this->isNewRecord()) {
            return $this->insert();
        } else {
            return $this->update();
        }
    }

    /**
     * @return bool
     */
    protected function insert()
    {
        $into = [];
        $params = [];
        foreach (array_keys($this->attributes) as $attr) {
            $into[] = $attr;
            $params[':' . $attr] = $this->attributes[$attr];
        }


        $into = implode(',', $into);
        $values = implode(',', array_keys($params));
        $table = static::tableName();
        if (Base::$app->db->exec("INSERT INTO $table($into) VALUES ($values)", $params)) {
            $this->id = Base::$app->db->getLastInsertId();
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    protected function update()
    {
        $set = [];
        $params = [':id' => $this->id];
        foreach ($this->attributes as $attr => $val) {
            $set[] = $attr . '= :'.$attr;
            $params[':' . $attr] = $val;
        }
        $table = static::tableName();
        $set = implode(',', $set);
        $where = ' id = :id';
        return Base::$app->db->exec("UPDATE $table SET $set WHERE $where", $params);
    }

    /**
     * Удаляет из базы
     * @return bool
     */
    public function delete(): bool
    {
        $table = static::tableName();
        return Base::$app->db->exec("DELETE FROM $table WHERE id=:id", [
            ':id' => $this->id
        ]);
    }

    /**
     * @param $attributes
     * @return bool
     */
    public function load($attributes): bool
    {
        if (empty($attributes)) {
            return false;
        }
        Base::configure($this, $attributes);
        return true;
    }

    /**
     * @return bool
     */
    public function beforeSave(): bool
    {
        return true;
    }

    /**
     * @return ActiveQuery
     */
    public static function find(): ActiveQuery
    {
        return new ActiveQuery(['class' => static::class]);
    }

    /**
     * находит одну сущность
     * @param $condition
     * @return ActiveRecord
     */
    public static function findOne(array $condition = []): ActiveRecord
    {
        return (new ActiveQuery(['class' => static::class]))->where($condition)->one();
    }

    /**
     * находит список сущностей
     * @param $condition
     * @return ActiveRecord[]
     */
    public static function findAll(array $condition = []): array
    {
        return (new ActiveQuery(['class' => static::class]))->where($condition)->all();
    }

    /**
     * проверяет наличие атрибута в рекорде
     * @param $attr
     * @return bool
     */
    public static function hasAttribute($attr): bool
    {
        return in_array($attr, static::attributes());
    }

    /**
     * возвращает список атрибутов сущности
     * @return array
     */
    public abstract static function attributes(): array;

    /**
     * возвращает имя таблицы сущности
     * @return string
     */
    public abstract static function tableName(): string;

    /**
     * @return bool
     */
    public function isNewRecord(): bool
    {
        return !is_numeric($this->id);
    }
}