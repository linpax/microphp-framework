<?php /** MicroDbDriver */

namespace Micro\Logger\Driver;

use Micro\Base\Exception;
use Micro\Db\ConnectionInjector;
use Micro\Db\IConnection;

/**
 * DB logger class file.
 *
 * Writer logs in DB
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright (c) 2013 Oleg Lunegov
 * @license https://github.com/linpax/microphp-framework/blob/master/LICENSE
 * @package Micro
 * @subpackage Logger\Driver
 * @version 1.0
 * @since 1.0
 */
class DbDriver extends LoggerDriver
{
    /** @var string $tableName logger table name */
    public $tableName;
    /** @var IConnection $db */
    protected $db;


    /**
     * Constructor prepare DB
     *
     * @access public
     *
     * @param array $params configuration params
     *
     * @result void
     * @throws Exception
     */
    public function __construct(array $params = [])
    {
        parent::__construct($params);

        $this->tableName = !empty($params['table']) ? $params['table'] : 'logs';
        $this->db = (new ConnectionInjector)->build();

        if (!$this->db->tableExists($this->tableName)) {
            $this->db->createTable(
                $this->tableName,
                array(
                    '`id` INT AUTO_INCREMENT',
                    '`level` VARCHAR(20) NOT NULL',
                    '`message` TEXT NOT NULL',
                    '`date_create` INT NOT NULL',
                    'PRIMARY KEY(id)'
                ),
                'ENGINE=InnoDB DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci'
            );
        }
    }

    /**
     * Send log message into DB
     *
     * @access public
     *
     * @param integer $level level number
     * @param string $message message to write
     *
     * @return void
     */
    public function sendMessage($level, $message)
    {
        $this->db->insert($this->tableName, [
            'level' => $level,
            'message' => $message,
            'date_create' => $_SERVER['REQUEST_TIME']
        ]);
    }
}
