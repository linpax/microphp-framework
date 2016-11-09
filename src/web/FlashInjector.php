<?php /** MicroFlashInjector */

namespace Micro\Web;

use Micro\Base\Exception;
use Micro\Base\Injector;

/**
 * Class FlashInjector
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright (c) 2013 Oleg Lunegov
 * @license https://github.com/linpax/microphp-framework/blob/master/LICENSE
 * @package Micro
 * @subpackage Web
 * @version 1.0
 * @since 1.0
 */
class FlashInjector extends Injector
{
    /**
     * @return FlashMessage
     * @throws Exception
     */
    public function build()
    {
        $flash = $this->get('flash');

        if (!($flash instanceof FlashMessage)) {
            throw new Exception('Component `flash` not configured');
        }

        return $flash;
    }
}