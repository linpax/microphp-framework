<?php /** MicroRequest */

namespace Micro\Web;

/**
 * Request class file.
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright &copy; 2013 Oleg Lunegov
 * @license /LICENSE
 * @package Micro
 * @subpackage Web
 * @version 1.0
 * @since 1.0
 */
class Request implements IRequest
{
    /** @var bool $cli Is running as CLI */
    protected $cli;
    /** @var array $data Data from request */
    protected $data;


    /**
     * Constructor Request
     *
     * @access public
     *
     * @result void
     */
    public function __construct()
    {
        $this->cli = PHP_SAPI === 'cli';
    }

    /**
     * Get flag of running as CLI
     *
     * @access public
     *
     * @return bool
     */
    public function isCli()
    {
        return $this->cli;
    }

    /**
     * Check request is AJAX ?
     *
     * @access public
     *
     * @return bool
     */
    public function isAjax()
    {
        return strtolower(filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH')) === 'xmlhttprequest';
    }

    /**
     * Get request method
     *
     * @access public
     *
     * @return string
     */
    public function getMethod()
    {
        return filter_input(INPUT_SERVER, 'REQUEST_METHOD');
    }

    /**
     * Get user IP-address
     *
     * @access public
     *
     * @return string
     */
    public function getUserIP()
    {
        return ($ip = filter_input(INPUT_SERVER, 'REMOTE_ADDR')) ? $ip : '127.0.0.1';
    }

    /**
     * Get browser data from user user agent string
     *
     * @access public
     *
     * @param null|string $agent User agent string
     *
     * @return mixed
     */
    public function getBrowser($agent = null)
    {
        return get_browser($agent ?: filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'), true);
    }

    /**
     * Get arguments from command line
     *
     * @access public
     *
     * @param string $char -a .. -z option char
     * @param string $name --optionName_string
     * @param bool|null $required Required value?
     *
     * @return mixed
     */
    public function getOption($char = '', $name = '', $required = null)
    {
        if (!$char && !$name) {
            return false;
        }

        if ($char && (1 < strlen($char) || 1 !== preg_match('/^\w$/', $char))) {
            return false;
        }

        if ($name && (1 !== preg_match('/^\w+$/', $name))) {
            return false;
        }

        switch ($required) {
            case true:
                $char = $char ? $char.':' : $char;
                $name = $name ? $name.':' : $name;
                break;
            case false:
                $char = $char ? $char.'::' : $char;
                $name = $name ? $name.'::' : $name;
                break;
        }

        $argv = ($opts = getopt($char, [$name])) ? array_shift($opts) : [];

        return is_array($argv) ? array_shift($argv) : $argv;
    }

    /**
     * Get files mapper
     *
     * @access public
     *
     * @param string $className Class name of mapper
     *
     * @return mixed
     */
    public function getFiles($className = '\Micro\Web\Uploader')
    {
        return is_array($_FILES) ? new $className($_FILES) : false;
    }

    /**
     * Get all data from storage
     *
     * @access public
     *
     * @param string $name Storage name
     *
     * @return mixed
     */
    public function getStorage($name)
    {
        return $GLOBALS[$name];
    }

    /**
     * Set all data into storage
     *
     * @access public
     *
     * @param string $name Storage name
     * @param array $data Any data
     *
     * @return void
     */
    public function setStorage($name, array $data = [])
    {
        $GLOBALS[$name] = $data;
    }

    /**
     * Get value by key from query storage
     *
     * @access public
     *
     * @param string $name Key name
     *
     * @return bool
     */
    public function query($name)
    {
        return $this->getVar($name, '_GET');
    }

    /**
     * Get any var from Request storage
     *
     * @access public
     *
     * @param string $name Key name
     * @param string $storage Storage name
     *
     * @return bool
     */
    public function getVar($name, $storage)
    {
        return array_key_exists($name, $GLOBALS[$storage]) ? $GLOBALS[$storage][$name] : null;
    }

    /**
     * Get value by key from post storage
     *
     * @access public
     *
     * @param string $name Key name
     *
     * @return bool
     */
    public function post($name)
    {
        return $this->getVar($name, '_POST');
    }

    /**
     * Get value by key from cookie storage
     *
     * @access public
     *
     * @param string $name Key name
     *
     * @return bool
     */
    public function cookie($name)
    {
        return $this->getVar($name, '_COOKIE');
    }

    /**
     * Get value by key from session storage
     *
     * @access public
     *
     * @param string $name Key name
     *
     * @return bool
     */
    public function session($name)
    {
        return $this->getVar($name, '_SESSION');
    }

    /**
     * Get value by key from server storage
     *
     * @access public
     *
     * @param string $name Key name
     *
     * @return bool
     */
    public function server($name)
    {
        return $this->getVar($name, '_SERVER');
    }

    /**
     * Set value into query storage
     *
     * @access public
     *
     * @param string $name Key name
     * @param string $value Key value
     *
     * @return void
     */
    public function setQuery($name, $value)
    {
        $this->setVar($name, $value, '_GET');
    }

    /**
     * Set value into storage
     *
     * @access public
     *
     * @param string $name Key name
     * @param string $value Key value
     * @param string $storage Storage name
     *
     * @return void
     */
    public function setVar($name, $value, $storage)
    {
        $GLOBALS[$storage][$name] = $value;
    }

    /**
     * Set value into post storage
     *
     * @access public
     *
     * @param string $name Key name
     * @param string $value Key value
     *
     * @return void
     */
    public function setPost($name, $value)
    {
        $this->setVar($name, $value, '_POST');
    }

    /**
     * Set value into cookie storage
     *
     * @access public
     *
     * @param string $name Key name
     * @param string $value Key value
     *
     * @return void
     */
    public function setCookie($name, $value)
    {
        $this->setVar($name, $value, '_COOKIE');
    }

    /**
     * Set value into session storage
     *
     * @access public
     *
     * @param string $name Key name
     * @param string $value Key value
     *
     * @return void
     */
    public function setSession($name, $value)
    {
        $this->setVar($name, $value, '_SESSION');
    }

    /**
     * Unset var into query storage
     *
     * @access public
     *
     * @param string $name Key name
     *
     * @return void
     */
    public function unsetQuery($name)
    {
        $this->unsetVar($name, '_GET');
    }

    /**
     * Unset var into storage
     *
     * @access public
     *
     * @param string $name Key name
     * @param string $storage Storage name
     *
     * @return void
     */
    public function unsetVar($name, $storage)
    {
        unset($GLOBALS[$storage][$name]);
    }

    /**
     * Unset var into post storage
     *
     * @access public
     *
     * @param string $name Key name
     *
     * @return void
     */
    public function unsetPost($name)
    {
        $this->unsetVar($name, '_POST');
    }

    /**
     * Unset var into session storage
     *
     * @access public
     *
     * @param string $name Key name
     *
     * @return void
     */
    public function unsetSession($name)
    {
        $this->unsetVar($name, '_SESSION');
    }

    /**
     * Get RequestPayload (RAW DATA)
     *
     * @return string|bool
     */
    public function getRequestPayload()
    {
        return file_get_contents('php://input');
    }
}
