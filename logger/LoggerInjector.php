<?php /** MicroLoggerInjector */

namespace Micro\Logger;

use Micro\Base\Injector;

/**
 * Class LoggerInjector
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright &copy; 2013 Oleg Lunegov
 * @license /LICENSE
 * @package Micro
 * @subpackage Logger
 * @version 1.0
 * @since 1.0
 */
class LoggerInjector extends Injector
{
    /**
     * @access public
     * @return Logger|bool
     */
    public function get()
    {
        return parent::get('logger');
    }

}