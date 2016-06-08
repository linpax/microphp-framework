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

    /** @var array $get $_GET from request */
    protected $get;
    /** @var array $data $_POST from request */
    protected $data;
    /** @var array $files $_FILES from request */
    protected $files;
    /** @var array $options $_SERVER from request */
    protected $options;


    /**
     * Constructor Request
     *
     * @access public
     *
     * @param array $get
     * @param array $post
     * @param array $files
     * @param array $server
     *
     * @result void
     */
    public function __construct(array $get = [], array $post = [], array $files = [], array $server = [])
    {
        $this->cli = PHP_SAPI === 'cli';

        $this->get = $get ?: $_GET;
        $this->data = $post ?: $_POST;
        $this->files = $files ?: $_FILES;
        $this->options = $server ?: $_SERVER;
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
        return $this->files ? new $className($this->files) : false;
    }

    /**
     * Get value by key from query storage
     *
     * @access public
     *
     * @param string $name Key name
     * @param integer $filter
     * @param mixed $options
     *
     * @return bool
     */
    public function query($name, $filter = FILTER_DEFAULT, $options = null)
    {
        if (array_key_exists($name, $this->get)) {
            return filter_var($this->get[$name], $filter, $options);
        }

        return null;
    }

    /**
     * Set query by key
     *
     * @access public
     *
     * @param string $name
     * @param string|integer $value
     *
     * @return void
     */
    public function setQuery($name, $value)
    {
        $this->get[$name] = $value;
    }

    /**
     * Get value by key from post storage
     *
     * @access public
     *
     * @param string $name Key name
     * @param integer $filter
     * @param mixed $options
     *
     * @return mixed
     */
    public function post($name, $filter = FILTER_DEFAULT, $options = null)
    {
        return filter_input(INPUT_POST, $name, $filter, $options);
    }

    /**
     * Get value by key from cookie storage
     *
     * @access public
     *
     * @param string $name Key name
     * @param integer $filter
     * @param mixed $options
     *
     * @return bool
     */
    public function cookie($name, $filter = FILTER_DEFAULT, $options = null)
    {
        return filter_input(INPUT_COOKIE, $name, $filter, $options);
    }

    /**
     * Get value by key from session storage
     *
     * @access public
     *
     * @param string $name Key name
     * @param integer $filter
     * @param mixed $options
     *
     * @return mixed
     */
    public function session($name, $filter = FILTER_DEFAULT, $options = null)
    {
        if (array_key_exists($name, $_SESSION)) {
            return filter_var($_SESSION[$name], $filter, $options);
        }

        return null;
    }

    /**
     * Unset value by key from session storage
     *
     * @access public
     *
     * @param string $name Key name
     *
     * @return void
     */
    public function unsetSession($name)
    {
        if (array_key_exists($name, $_SESSION)) {
            unset($_SESSION[$name]);
        }
    }


    /**
     * Get value by key from server storage
     *
     * @access public
     *
     * @param string $name Key name
     * @param integer $filter
     * @param mixed $options
     *
     * @return bool
     */
    public function server($name, $filter = FILTER_DEFAULT, $options = null)
    {
        return filter_input(INPUT_SERVER, $name, $filter, $options);
    }

    /**
     * Get RequestPayload (RAW DATA)
     *
     * @return string|bool
     */
    public function requestPayload()
    {
        return file_get_contents('php://input');
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
        $_SESSION[$name] = $value;
    }
}
