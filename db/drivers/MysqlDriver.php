<?php /** MysqlDriverMicro */

namespace Micro\Db\Drivers;

use Micro\Base\Exception;

/**
 * MySQL Driver class file.
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright (c) 2013 Oleg Lunegov
 * @license https://github.com/linpax/microphp-framework/blob/master/LICENSE
 * @package Micro
 * @subpackage Db\Drivers
 * @version 1.0
 * @since 1.0
 */
class MysqlDriver extends Driver
{
    /** @var \PDO|null $conn Connection to DB */
    protected $conn;
    /** @var string $tableSchema Table schema for postgres */
    protected $tableSchema = 'public';


    /**
     * Construct for this class
     *
     * @access public
     *
     * @param array $config
     * @param array $options
     *
     * @result void
     * @throws Exception
     */
    public function __construct(array $config = [], array $options = [])
    {
        parent::__construct();

        if (!empty($config['schema'])) {
            $this->tableSchema = $config['schema'];
        }

        try {
            $this->conn = new \PDO($config['dsn'], $config['username'], $config['password'], $options);
            $this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        } catch (\PDOException $e) {
            if (!array_key_exists('ignoreFail', $config) || !$config['ignoreFail']) {
                throw new Exception('Connect to DB failed: '.$e->getMessage());
            }
        }
    }

    /**
     * Destructor for this class
     *
     * @access public
     * @return void
     */
    public function __destruct()
    {
        $this->conn = null;
    }

    /**
     * Send RAW query to DB
     *
     * @access public
     *
     * @param string $query raw query to db
     * @param array $params params for query
     * @param int $fetchType fetching type
     * @param string $fetchClass fetching class
     *
     * @return \PDOStatement|array
     * @throws Exception
     */
    public function rawQuery($query = '', array $params = [], $fetchType = \PDO::FETCH_ASSOC, $fetchClass = 'Model')
    {
        $sth = $this->conn->prepare($query);

        if ($fetchType === \PDO::FETCH_CLASS) {
            /** @noinspection PhpMethodParametersCountMismatchInspection */
            $sth->setFetchMode($fetchType, $fetchClass, ['new' => false]);
        } else {
            $sth->setFetchMode($fetchType);
        }

        foreach ($params AS $name => $value) {
            $sth->bindValue($name, $value);
        }

        $sth->execute();

        return $sth->fetchAll();
    }

    /**
     * List database names on this connection
     *
     * @access public
     * @return array|boolean
     */
    public function listDatabases()
    {
        $sql = 'SHOW DATABASES;';

        if ($this->getDriverType() === 'pgsql') {
            $sql = 'SELECT datname FROM pg_database;';
        }

        $sth = $this->conn->query($sql);
        $result = [];

        foreach ($sth->fetchAll() AS $row) {
            $result[] = $row[0];
        }

        return $result;
    }

    public function getDriverType()
    {
        return $this->conn->getAttribute(\PDO::ATTR_DRIVER_NAME);
    }

    /**
     * Info of database
     *
     * @access public
     * @param string $dbName Database name
     * @return array
     */
    public function infoDatabase($dbName)
    {
        $sth = $this->conn->query("SHOW TABLE STATUS FROM {$dbName};");

        $result = [];
        foreach ($sth->fetchAll() AS $row) {
            $result[] = [
                'name' => $row['Name'],
                'engine' => $row['Engine'],
                'rows' => $row['Rows'],
                'length' => $row['Avg_row_length'],
                'increment' => $row['Auto_increment'],
                'collation' => $row['Collation']
            ];
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function tableExists($table)
    {
        return in_array($table, $this->listTables(), false);
    }

    /**
     * @inheritdoc
     */
    public function listTables()
    {
        $sql = 'SHOW TABLES;';

        if ($this->getDriverType() == 'pgsql') {
            $sql = 'SELECT table_name FROM information_schema.tables WHERE table_schema = \'' . $this->tableSchema . '\'';
        }

        return $this->conn->query($sql)->fetchAll(\PDO::FETCH_COLUMN, 0);
    }

    /**
     * @inheritdoc
     */
    public function createTable($name, array $elements = [], $params = '')
    {
        return $this->conn->exec(
            sprintf('SELECT TABLE IF NOT EXISTS `%s` (%s) %s;', $name, implode(',', $elements), $params)
        );
    }

    /**
     * @inheritdoc
     */
    public function clearTable($name)
    {
        return $this->conn->exec("TRUNCATE {$name};");
    }

    /**
     * @inheritdoc
     */
    public function removeTable($name)
    {
        return $this->conn->exec("DROP TABLE {$name};");
    }

    /**
     * @inheritdoc
     */
    public function fieldExists($field, $table)
    {
        foreach ($this->listFields($table) AS $tbl) {
            if ($tbl['field'] === $field) {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function listFields($table)
    {
        if ($this->getDriverType() === 'pgsql') {
            $sth = $this->conn->query('SELECT * FROM information_schema.columns WHERE table_name =\'categories\'');

            $result = [];
            foreach ($sth->fetchAll(\PDO::FETCH_ASSOC) as $row) {
                $result[] = [
                    'field' => $row['column_name'],
                    'type' => $row['data_type'] . (($max = $row['character_maximum_length']) ? '(' . $max . ')' : ''),
                    'null' => $row['is_nullable'],
                    'default' => $row['column_default']
                ];
            }

            return $result;
        }

        $sth = $this->conn->query("SHOW COLUMNS FROM {$table};");

        $result = [];
        foreach ($sth->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $result[] = [
                'field' => $row['Field'],
                'type' => $row['Type'],
                'null' => $row['Null'],
                'key' => $row['Key'],
                'default' => $row['Default'],
                'extra' => $row['Extra']
            ];
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function fieldInfo($field, $table)
    {
        return $this->conn->query("SELECT {$field} FROM {$table} LIMIT 1;")->getColumnMeta(0);
    }

    /**
     * @inheritdoc
     */
    public function switchDatabase($dbName)
    {
        if ($this->conn->exec("USE {$dbName};") !== false) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function insert($table, array $line = [], $multi = false)
    {
        $fields = '`' . implode('`, `', array_keys($multi ? $line[0] : $line)) . '`';

        if ($this->getDriverType() === 'pgsql') {
            $fields = '"' . implode('", "', array_keys($multi ? $line[0] : $line)) . '"';
        }

        $values = ':'.implode(', :', array_keys($multi ? $line[0] : $line));
        $rows = $multi ? $line : [$line];
        $id = null;

        if ($rows) {
            $this->conn->beginTransaction();

            $dbh = null;
            foreach ($rows AS $row) {
                $dbh = $this->conn->prepare("INSERT INTO {$table} ({$fields}) VALUES ({$values});")->execute($row);
            }

            $id = $dbh ? $this->conn->lastInsertId() : false;
            $this->conn->commit();
        }

        return $id ?: false;
    }

    /**
     * @inheritdoc
     */
    public function update($table, array $elements = [], $conditions = '')
    {
        $keys = array_keys($elements);
        if (0 === count($keys)) {
            return false;
        }

        $valStr = [];
        foreach ($keys as $key) {
            $valStr[] = '`'.$key.'` = :'.$key;
        }
        $valStr = implode(',', $valStr);

        if ($conditions) {
            $conditions = 'WHERE '.$conditions;
        }

        return $this->conn->prepare("UPDATE {$table} SET {$valStr} {$conditions};")->execute($elements);
    }

    /**
     * @inheritdoc
     */
    public function delete($table, $conditions, array $ph = [])
    {
        return $this->conn->prepare("DELETE FROM {$table} WHERE {$conditions};")->execute($ph);
    }

    /**
     * @inheritdoc
     */
    public function exists($table, array $params = [])
    {
        $keys = [];
        foreach ($params AS $key => $val) {
            $keys[] = $table . '.' . $key . '=\'' . $val . '\'';
        }

        $sth = $this->conn->prepare('SELECT * FROM ' . $table . ' WHERE ' . implode(' AND ', $keys) . ' LIMIT 1;');
        /** @noinspection PdoApiUsageInspection */
        $sth->execute();

        return (bool)$sth->rowCount();
    }

    /**
     * @inheritdoc
     */
    public function count($subQuery = '', $table = '')
    {
        if ($subQuery) {
            $sth = $this->conn->prepare("SELECT COUNT(*) FROM ({$subQuery}) AS m;");
        } elseif ($table) {
            $sth = $this->conn->prepare("SELECT COUNT(*) FROM {$table} AS m;");
        } else {
            return false;
        }
        if ($sth->execute()) {
            return $sth->fetchColumn();
        }

        return false;
    }
}
