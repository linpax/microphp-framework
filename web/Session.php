<?php /** MicroSession */

namespace Micro\Web;

/**
 * Session is a Session manager
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright (c) 2013 Oleg Lunegov
 * @license https://github.com/linpax/microphp-framework/blob/master/LICENSE
 * @package Micro
 * @subpackage Web
 * @version 1.0
 * @since 1.0
 *
 * @property array $flash FlashMessages
 */
class Session extends \stdClass implements ISession
{
    /** @var IRequest $request */
    protected $request;


    /**
     * Construct for this class
     *
     * @access public
     *
     * @param IRequest $request
     * @param bool $autoStart
     *
     * @result void
     */
    public function __construct(IRequest $request, $autoStart = false)
    {
        $this->request = $request;

        if ($autoStart === true) {
            $this->create();
        }
    }

    /**
     * Create a new session or load prev session
     *
     * @access public
     * @return void
     */
    public function create()
    {
        if (PHP_SESSION_ACTIVE !== session_status()) {
            session_start();
        }
    }

    /**
     * Destroy session
     *
     * @access public
     * @return void
     */
    public function destroy()
    {
        if (PHP_SESSION_ACTIVE === session_status()) {
            session_unset();
            session_destroy();
        }
    }

    /**
     * Getter session element
     *
     * @access public
     *
     * @param string $name element name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->request->session($name);
    }

    /**
     * Setter session element
     *
     * @access public
     *
     * @param string $name element name
     * @param mixed $value element value
     *
     * @return void
     */
    public function __set($name, $value)
    {
        $this->request->setSession($name, $value);
    }

    /**
     * Is set session element
     *
     * @access public
     *
     * @param string $name element name
     *
     * @return boolean
     */
    public function __isset($name)
    {
        return (bool)$this->request->session($name);
    }

    /**
     * Unset session element
     *
     * @access public
     *
     * @param string $name element name
     *
     * @return void
     */
    public function __unset($name)
    {
        $this->request->unsetSession($name);
    }
}
