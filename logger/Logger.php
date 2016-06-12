<?php /** MicroLogger */

namespace Micro\Logger;

use Micro\Logger\Driver\ILoggerDriver;

/**
 * Logger manager
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright (c) 2013 Oleg Lunegov
 * @license https://github.com/linpax/microphp-framework/blob/master/LICENSE
 * @package Micro
 * @subpackage Logger
 * @version 1.0
 * @since 1.0
 */
class Logger
{
    /** @var array $supportedLevels supported logger levels */
    public static $supportedLevels = [
        'emergency',
        'alert',
        'critical',
        'error',
        'warning',
        'notice',
        'info',
        'debug'
    ];

    /** @var ILoggerDriver[] $loggers defined loggers */
    protected $loggers = array();


    /**
     * Export loggers
     *
     * @access public
     *
     * @param array $loggers
     *
     * @result void
     */
    public function __construct(array $loggers = [])
    {
        foreach ($loggers AS $name => $log) {
            if (empty($log['class']) || !class_exists($log['class'])) {
                continue;
            }

            if (empty($log['levels'])) {
                continue;
            }

            $this->loggers[$name] = new $log['class']($log);
        }
    }

    /**
     * Send message to loggers
     *
     * @access public
     *
     * @param string $level logger level
     * @param string $message message to write
     *
     * @result void
     * @throws \Micro\Base\Exception
     */
    public function send($level, $message)
    {
        foreach ($this->loggers AS $log) {
            /** @var ILoggerDriver $log logger */
            if ($log->isSupportedLevel($level)) {
                $log->sendMessage($level, $message);
            }
        }
    }
}
