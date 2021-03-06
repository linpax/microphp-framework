<?php /** MicroCookie */

namespace Micro\Web;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Cookie class file.
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
class Cookie implements ICookie
{
    /** @var ServerRequestInterface $request */
    protected $request;


    /**
     * Constructor of object
     *
     * @access public
     *
     * @param ServerRequestInterface $request
     *
     * @result void
     */
    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * Get cookie
     *
     * @access public
     *
     * @param string $name cookie name
     *
     * @return mixed|bool
     */
    public function get($name)
    {
        $cookie = $this->request->getCookieParams();

        return array_key_exists($name, $cookie) ? $cookie[$name] : null;
    }

    /**
     * Delete cookie
     *
     * @access public
     *
     * @param string $name cookie name
     *
     * @return bool
     */
    public function del($name)
    {
        if ($this->exists($name)) {
            return $this->set($name, false, time() - 3600);
        }

        return false;
    }

    /**
     * Exists cookie
     *
     * @access public
     *
     * @param string $name cookie name
     *
     * @return bool
     */
    public function exists($name)
    {
        return array_key_exists($name, $this->request->getCookieParams());
    }

    /**
     * Set cookie
     *
     * @access public
     *
     * @param string $name cookie name
     * @param mixed $value data value
     * @param int $expire life time
     * @param string $path path access cookie
     * @param string $domain domain access cookie
     * @param bool $secure use SSL?
     * @param bool $httponly disable on JS?
     *
     * @return bool
     */
    public function set($name, $value, $expire = 0, $path = '/', $domain = '', $secure = false, $httponly = true)
    {
        return setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
    }
}
