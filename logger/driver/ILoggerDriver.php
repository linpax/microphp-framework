<?php /** MicroInterfaceLoggerDriver */

namespace Micro\Logger\Driver;

/**
 * Interface ILoggerDriver
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
interface ILoggerDriver
{
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
    public function sendMessage($level, $message);

    /**
     * Check support level
     *
     * @access public
     *
     * @param integer $level level number
     *
     * @return bool
     */
    public function isSupportedLevel($level);
}