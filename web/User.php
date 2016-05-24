<?php /** MicroUser */

namespace Micro\Web;


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
            return (new Injector)->get('permission')->check($this->getID(), $permission, $data);
        } else {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function isGuest()
    {
        return !(new Injector)->get('session') || !(new Injector)->get('session')->UserID;
    }

    /**
     * @inheritdoc
     */
    public function getID()
    {
        return (!$this->isGuest()) ? (new Injector)->get('session')->UserID : false;
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
        (new Injector)->get('session')->UserID = $id;
    }

    /**
     * @inheritdoc
     */
    public function logout()
    {
        if (!$this->isGuest()) {
            $this->setID(null);
            (new Injector)->get('session')->destroy();
        }
    }

    /**
     * @inheritdoc
     */
    public function getCaptcha()
    {
        return (new Injector)->get('session')->captchaCode;
    }

    /**
     * @inheritdoc
     */
    public function setCaptcha($code)
    {
        (new Injector)->get('session')->captchaCode = md5($code);
    }

    /**
     * @inheritdoc
     */
    public function checkCaptcha($code)
    {
        if (!(new Injector)->get('session')->captchaCode) {
            return null;
        }

        return (new Injector)->get('session')->captchaCode === md5($code);
    }
}
