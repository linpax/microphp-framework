<?php /** MicroCookieInjector */

namespace Micro\Web;

use Micro\Base\Exception;
use Micro\Base\Injector;

/**
 * Class CookieInjector
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
class CookieInjector extends Injector
{
    /**
     * @access public
     * @return ICookie
     * @throws Exception
     */
    public function build()
    {
        $cookie = $this->get('cookie');

        if (!($cookie instanceof ICookie)) {
            throw new Exception('Component `cookie` not configured');
        }

        return $cookie;
    }
}