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
    /**
     * @access public
     * @result void
     */
    public function __construct()
    {
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
            return (new AuthInjector)->get()->check($this->getID(), $permission, $data);
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
        return !(new SessionInjector)->get() || !(new SessionInjector)->get()->UserID;
    }

    /**
     * @return bool|int
     * @throws Exception
     */
    public function getID()
    {
        return (!$this->isGuest()) ? (new SessionInjector)->get()->UserID : false;
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
        (new SessionInjector)->get()->UserID = $id;
    }

    /**
     * @throws Exception
     */
    public function logout()
    {
        if (!$this->isGuest()) {
            $this->setID(null);
            (new SessionInjector)->get()->destroy();
        }
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getCaptcha()
    {
        return (new SessionInjector)->get()->captchaCode;
    }

    /**
     * @param string $code
     * @throws Exception
     */
    public function setCaptcha($code)
    {
        (new SessionInjector)->get()->captchaCode = md5($code);
    }

    /**
     * @param string $code
     * @return bool|null
     * @throws Exception
     */
    public function checkCaptcha($code)
    {
        if (!(new SessionInjector)->get()->captchaCode) {
            return null;
        }

        return (new SessionInjector)->get()->captchaCode === md5($code);
    }
}
