<?php /** MicroLoggerInjector */

namespace Micro\Logger;

use Micro\Base\Exception;

/**
 * Class LoggerInjector
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
class Injector extends \Micro\Base\Injector
{
    /**
     * @access public
     * @return Adapter
     * @throws Exception
     */
    public function build()
    {
        $logger = $this->get('logger');

        if (!($logger instanceof Adapter)) {
            throw new Exception('Component `logger` not configured');
        }

        return $logger;
    }

}