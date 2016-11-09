<?php /** MicroLoggerDriverInterface */

namespace Micro\Logger\Drivers;

use Micro\Base\Exception;
use Micro\Logger\Adapter;

/**
 * Base loggerDriver class file.
 *
 * Interface for loggers
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
abstract class LoggerDriver implements Adapter
{
    /** @var array $supportedLevels supported log levels */
    protected $supportedLevels = [];


    /**
     * Constructor is a initialize logger
     *
     * @access public
     *
     * @param array $params configuration params
     *
     * @throws Exception
     * @result void
     */
    public function __construct(array $params = [])
    {
        $levels = explode(',', str_replace(' ', '', strtolower($params['levels'])));

        foreach ($levels AS $level) {
            if (in_array($level, Logger::$supportedLevels, true)) {
                $this->supportedLevels[] = $level;
            }
        }
    }

    /**
     * Check support level
     *
     * @access public
     *
     * @param integer $level level number
     *
     * @return bool
     */
    public function isSupportedLevel($level)
    {
        return in_array($level, $this->supportedLevels, false) === false ?: true;
    }
}
