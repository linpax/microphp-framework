<?php /** MicroUser */

namespace Micro\Web;

use Micro\Auth\AuthInjector;
use Micro\Base\Exception;


/**
 * Micro user class file
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
class User implements IUser
{
    protected $session;

    /**
     * @access public
     * @param ISession $session
     * @result void
     */
    public function __construct(ISession $session)
    {
        $this->session = $session;
    }

    /**
     * @param string $permission
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public function check($permission, array $data = [])
    {
        if (!$this->isGuest()) {
            return (new AuthInjector)->build()->check($this->getID(), $permission, $data);
        } else {
            return false;
        }
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function isGuest()
    {
        return !$this->session->UserID;
    }

    /**
     * @return bool|int
     * @throws Exception
     */
    public function getID()
    {
        return (!$this->isGuest()) ? $this->session->UserID : false;
    }

    /**
     * @param int|string $userId
     * @throws Exception
     */
    public function login($userId)
    {
        $this->setID($userId);
    }

    /**
     * @param mixed $id
     * @throws Exception
     */
    public function setID($id)
    {
        $this->session->UserID = $id;
    }

    /**
     * @throws Exception
     */
    public function logout()
    {
        if (!$this->isGuest()) {
            $this->setID(null);
            $this->session->destroy();
        }
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getCaptcha()
    {
        return $this->session->captchaCode;
    }

    /**
     * @param string $code
     * @throws Exception
     */
    public function setCaptcha($code)
    {
        $this->session->captchaCode = md5($code);
    }

    /**
     * @param string $code
     * @return bool|null
     * @throws Exception
     */
    public function checkCaptcha($code)
    {
        if (!$this->session->captchaCode) {
            return null;
        }

        return $this->session->captchaCode === md5($code);
    }
}
