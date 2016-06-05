<?php /** MicroUser */

namespace Micro\Web;

use Micro\Auth\AuthInjector;


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
     * @inheritdoc
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
     * @inheritdoc
     */
    public function isGuest()
    {
        return !(new SessionInjector)->get() || !(new SessionInjector)->get()->UserID;
    }

    /**
     * @inheritdoc
     */
    public function getID()
    {
        return (!$this->isGuest()) ? (new SessionInjector)->get()->UserID : false;
    }

    /**
     * @inheritdoc
     */
    public function login($userId)
    {
        $this->setID($userId);
    }

    /**
     * @inheritdoc
     */
    public function setID($id)
    {
        (new SessionInjector)->get()->UserID = $id;
    }

    /**
     * @inheritdoc
     */
    public function logout()
    {
        if (!$this->isGuest()) {
            $this->setID(null);
            (new SessionInjector)->get()->destroy();
        }
    }

    /**
     * @inheritdoc
     */
    public function getCaptcha()
    {
        return (new SessionInjector)->get()->captchaCode;
    }

    /**
     * @inheritdoc
     */
    public function setCaptcha($code)
    {
        (new SessionInjector)->get()->captchaCode = md5($code);
    }

    /**
     * @inheritdoc
     */
    public function checkCaptcha($code)
    {
        if (!(new SessionInjector)->get()->captchaCode) {
            return null;
        }

        return (new SessionInjector)->get()->captchaCode === md5($code);
    }
}
