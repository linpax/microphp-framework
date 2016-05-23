<?php /** MicroFileDriver */

namespace Micro\Logger\Driver;

use Micro\Base\Exception;

/**
 * FileDriver logger class file.
 *
 * Writer logs in file
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright &copy; 2013 Oleg Lunegov
 * @license /LICENSE
 * @package Micro
 * @subpackage Logger\Driver
 * @version 1.0
 * @since 1.0
 */
class FileDriver extends LoggerDriver
{
    /** @var resource $connect File handler */
    protected $connect;


    /**
     * Open file for write messages
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

        if (is_writable($params['filename']) || is_writable(dirname($params['filename']))) {
            $this->connect = fopen($params['filename'], 'a+');
        } else {
            throw new Exception('Directory or file "'.$params['filename'].'" is read-only');
        }
    }

    /**
     * Send message in log
     *
     * @access public
     *
     * @param integer $level level number
     * @param string $message message to write
     *
     * @result void
     * @throws \Micro\Base\Exception
     */
    public function sendMessage($level, $message)
    {
        if (is_resource($this->connect)) {
            fwrite($this->connect, '['.date('H:i:s d.m.Y').'] '.ucfirst($level).": {$message}\n");
        } else {
            throw new Exception('Error write log in file.');
        }
    }

    /**
     * Close opened for messages file
     *
     * @access public
     * @result void
     */
    public function __destruct()
    {
        if (is_resource($this->connect)) {
            fclose($this->connect);
        }
    }
}
