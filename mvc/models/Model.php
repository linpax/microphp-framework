<?php /** MicroModel */

namespace Micro\Mvc\Models;

use Micro\Base\Exception;
use Micro\Db\ConnectionInjector;
use Micro\File\Type;
use Micro\Form\FormModel;

/**
 * Model class file.
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright (c) 2013 Oleg Lunegov
 * @license https://github.com/linpax/microphp-framework/blob/master/LICENSE
 * @package Micro
 * @subpackage Mvc\Models
 * @version 1.0
 * @since 1.0
 * @abstract
 */
abstract class Model extends FormModel implements IModel
{
    /** @var string $primaryKey Primary key on table */
    public static $primaryKey = 'id';
    /** @var string $tableName Table name */
    public static $tableName;

    /** @var boolean $_isNewRecord Is new record? */
    protected $_isNewRecord = false;
    /** @var array $cacheRelations Cached loads relations */
    protected $cacheRelations = [];


    /**
     * Constructor for model
     *
     * @access public
     *
     * @param boolean $new is new model?
     *
     * @result void
     * @throws Exception
     */
    public function __construct($new = true)
    {
        parent::__construct();

        if (!static::$tableName) {
            throw new Exception('Table name not set in `'.__CLASS__.'`` model.');
        }

        $this->_isNewRecord = $new;
    }

    /**
     * Finder by primary key
     *
     * @access public
     *
     * @param int|string $value unique value
     *
     * @return mixed
     * @throws \Micro\base\Exception
     * @static
     */
    public static function findByPk($value)
    {
        return self::findByAttributes([self::$primaryKey => $value], true);
    }

    /**
     * Find models by attributes
     *
     * @access public
     *
     * @param array $attributes attributes and data for search
     * @param bool $single single or more
     *
     * @return mixed
     * @throws \Micro\base\Exception
     */
    public static function findByAttributes(array $attributes = [], $single = false)
    {
        $query = new Query((new ConnectionInjector)->getDriver());
        foreach ($attributes AS $key => $val) {
            $query->addWhere($key.' = :'.$key);
        }
        $query->params = $attributes;

        return self::finder($query, $single);
    }

    /**
     * Finder data in DB
     *
     * @access public
     *
     * @param IQuery $query query to search
     * @param boolean $single is single
     *
     * @return mixed One or more data
     * @throws \Micro\base\Exception
     * @static
     */
    public static function finder(IQuery $query = null, $single = false)
    {
        $query = ($query instanceof Query) ? $query : new Query((new ConnectionInjector)->getDriver());
        $query->table = static::$tableName . ' m';
        $query->objectName = get_called_class();
        $query->single = $single;

        return $query->run();
    }

    /**
     * Find by model attribute values
     *
     * @access public
     *
     * @param bool $single Is a single?
     *
     * @return mixed
     * @throws \Micro\base\Exception
     */
    public function find($single = false)
    {
        return self::findByAttributes(Type::getVars($this), $single);
    }

    /**
     * Get attributes defined into model
     *
     * @access public
     *
     * @return array
     * @throws Exception
     */
    public function getAttributes()
    {
        $fields = [];
        foreach ((new ConnectionInjector)->getDriver()->listFields(static::$tableName) AS $field) {
            $fields[] = $field['field'];
        }

        return $fields;
    }

    /**
     * Get relation data or magic properties
     *
     * @access public
     *
     * @param string $name
     *
     * @return mixed
     * @throws Exception
     */
    public function __get($name)
    {
        /** @var array $relation */
        if ($relation = $this->relations()->get($name)) {
            if (empty($this->cacheRelations[$name])) {
                $sql = new Query((new ConnectionInjector)->getDriver());

                if ((new ConnectionInjector)->build()->getDriver()->getDriverType() === 'pgsql') {
                    $sql->addWhere('"m"."' . $relation['On'][1] . '"=:' . $relation['On'][0]);
                } else {
                    $sql->addWhere('`m`.`' . $relation['On'][1] . '`=:' . $relation['On'][0]);
                }

                if ($relation['Where']) {
                    $sql->addWhere($relation['Where']);
                }
                if ($relation['Params']) {
                    $sql->params = $relation['Params'];
                }
                if ($relation['Limit'] > 0) {
                    $sql->limit = $relation['Limit'];
                }

                $sql->params[$relation['On'][0]] = $this->{$relation['On'][0]};

                /** @noinspection PhpUndefinedMethodInspection */
                $this->cacheRelations[$name] = $relation['Model']::finder($sql, !$relation['IsMany']);
            }

            return $this->cacheRelations[$name];
        } elseif (isset($this->$name)) {
            return $this->$name;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function relations()
    {
        return new Relations;
    }

    /**
     * Save changes
     *
     * @access public
     *
     * @param bool $validate Validated data?
     *
     * @return boolean
     * @throws Exception
     */
    final public function save($validate = false)
    {
        if ($validate && !$this->validate()) {
            return false;
        }

        if ($this->isNewRecord()) {
            return $this->create();
        } else {
            if ($this->beforeSave() && $this->update()) {
                $this->afterSave();

                return true;
            }
        }

        return false;
    }

    /**
     * Is new record?
     *
     * @access public
     * @return boolean
     */
    public function isNewRecord()
    {
        return $this->_isNewRecord;
    }

    /**
     * Create changes
     *
     * @access public
     * @return boolean
     * @throws Exception
     */
    final public function create()
    {
        if (!$this->isNewRecord()) {
            return false;
        }
        if ($this->beforeCreate() && $this->beforeSave()) {
            $id = (new ConnectionInjector)->build()->getDriver()->insert(static::$tableName,
                $this->mergeAttributesDb());
            if (!$id) {
                return false;
            }

            $pKey = self::$primaryKey ?: 'id';
            if ($this->checkAttributeExists($pKey)) {
                $this->$pKey = $id;
            }

            $this->_isNewRecord = false;

            $this->afterCreate();
            $this->afterSave();

            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function beforeCreate()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function beforeSave()
    {
        return true;
    }

    /**
     * Merge local attributes and db attributes
     *
     * @access protected
     *
     * @return array
     * @throws \Micro\base\Exception
     */
    protected function mergeAttributesDb()
    {
        $arr = Type::getVars($this);

        $buffer = [];
        foreach ((new ConnectionInjector)->build()->getDriver()->listFields(static::$tableName) AS $row) {
            $buffer[] = $row['field'];
        }

        foreach ($arr AS $key => $val) {
            if (!in_array($key, $buffer, true)) {
                unset($arr[$key]);
            }
        }

        unset($arr['isNewRecord']);

        return $arr;
    }

    /**
     * Check attribute exists into table
     *
     * @access public
     *
     * @param string $name Attribute name
     *
     * @return boolean
     * @throws Exception
     */
    public function checkAttributeExists($name)
    {
        if (isset($this->$name)) {
            return true;
        }

        $res = false;
        foreach ((new ConnectionInjector)->build()->getDriver()->listFields(static::$tableName) AS $row) {
            if ($row['field'] === $name) {
                $res = true;
                break;
            }
        }

        return $res;
    }

    /**
     * @inheritdoc
     */
    public function afterCreate()
    {
    }

    /**
     * @inheritdoc
     */
    public function afterSave()
    {
    }

    /**
     * Update changes
     *
     * @access public
     *
     * @param string $where condition for search
     *
     * @throws Exception
     * @return boolean
     */
    final public function update($where = null)
    {
        if ($this->isNewRecord()) {
            return false;
        }
        if ($this->beforeUpdate()) {
            if (!$where) {
                if (self::$primaryKey) {
                    if ((new ConnectionInjector)->build()->getDriverType() === 'pgsql') {
                        $where .= '"' . self::$primaryKey . '" = :' . self::$primaryKey;
                    } else {
                        $where .= '`' . self::$primaryKey . '` = :' . self::$primaryKey;
                    }

                } else {
                    throw new Exception('In table '.static::$tableName.' option `'.self::$primaryKey.'` not defined/not use.'
                    );
                }
            }

            if ((new ConnectionInjector)->build()->update(static::$tableName, $this->mergeAttributesDb(), $where)) {
                $this->afterUpdate();

                return true;
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function beforeUpdate()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function afterUpdate()
    {
    }

    /**
     * Delete changes
     *
     * @access public
     * @return boolean
     * @throws Exception
     */
    final public function delete()
    {
        if ($this->isNewRecord()) {
            return false;
        }
        if ($this->beforeDelete()) {
            if (!self::$primaryKey) {
                throw new Exception('In table '.static::$tableName.' option `'.self::$primaryKey.'` not defined/not use.');
            }

            if (
            (new ConnectionInjector)->build()->delete(
                static::$tableName,
                self::$primaryKey.'=:'.self::$primaryKey, [self::$primaryKey => $this->{self::$primaryKey}]
            )
            ) {
                $this->afterDelete();
                unset($this);

                return true;
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function afterDelete()
    {
    }
}
