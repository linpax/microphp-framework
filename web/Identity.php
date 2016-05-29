<?php /** MicroIdentity */

namespace Micro\Web;

use Micro\Base\Injector;

/**
 * Identity class file.
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright &copy; 2013 Oleg Lunegov
 * @license /LICENSE
 * @package Micro
 * @subpackage Web
 * @version 1.0
 * @since 1.0
 * @abstract
 */
abstract class Identity
{
    /** @var string $username user name */
    public $username;
    /** @var string $password user password */
    public $password;
    /** @var string $error error string */
    public $error;

    /**
     * Initialize identity element
     *
     * @access public
     *
     * @param string $username
     * @param string $password
     *
     * @result void
     */
    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
        $this->error = null;
    }

    /**
     * Authenticate
     *
     * @access public
     *
     * @return bool
     * @abstract
     */
    abstract public function authenticate();

    /**
     * Add data into session
     *
     * @access public
     *
     * @param string $name session parameter name
     * @param mixed $value session parameter value
     *
     * @return mixed
     */
    public function addSession($name, $value)
    {
        return (new Injector)->get('session')->$name = $value;
    }

    /**
     * Add data into cookie
     *
     * @access public
     *
     * @param string $name cookie name
     * @param mixed $value data value
     * @param int $expire life time
     * @param string $path path access cookie
     * @param string $domain domain access cookie
     * @param bool $secure use SSL?
     * @param bool $httpOnly disable on JS?
     *
     * @return mixed
     */
    public function addCookie(
        $name,
        $value,
        $expire = 0,
        $path = '/',
        $domain = '',
        $secure = false,
        $httpOnly = true
    ) {
        return (new Injector)->get('cookie')->set($name, $value, $expire, $path, $domain, $secure, $httpOnly);
    }
}
