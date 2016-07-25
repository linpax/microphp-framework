<?php /** AccessFilterMicro */

namespace Micro\Filter;

use Micro\Base\Exception;
use Micro\Web\IRequest;
use Micro\Web\IUser;
use Micro\Web\RequestInjector;
use Micro\Web\UserInjector;

/**
 * Class AccessFilter
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright (c) 2013 Oleg Lunegov
 * @license https://github.com/linpax/microphp-framework/blob/master/LICENSE
 * @package Micro
 * @subpackage Filter
 * @version 1.0
 * @since 1.0
 */
class AccessFilter extends Filter
{
    /** @var IUser $user */
    protected $user;
    /** @var IRequest $request */
    protected $request;

    /**
     * @param array $params
     * @return bool
     * @throws Exception
     */
    public function pre(array $params)
    {
        $this->user = (new UserInjector)->build();
        $this->request = (new RequestInjector)->build();

        /** @noinspection ForeachSourceInspection */
        foreach ($params['rules'] AS $rule) {
            $res = $this->checkRule($rule);

            if ($res === true) {
                return true;
            } elseif ($res === false) {
                $this->result = [
                    'redirect' => !empty($rule['redirect']) ? $rule['redirect'] : null,
                    'message' => !empty($rule['message']) ? $rule['message'] : 'Access denied!'
                ];

                return false;
            } elseif ($res === null) {
                continue;
            }
        }

        return true;
    }

    /**
     * Check one rule
     *
     * @access protected
     *
     * @param array $rule rule definition
     *
     * @return bool|null
     * @throws Exception
     */
    protected function checkRule(array $rule)
    {
        if (
            $this->matchAction($rule)
            && $this->matchUser($rule)
            && $this->matchRole($rule)
            && $this->matchIP($rule)
            && $this->matchVerb($rule)
        ) {
            return $rule['allow'];
        } else {
            return null;
        }
    }

    /**
     * Match action
     *
     * @access protected
     *
     * @param array $rule rule definition
     *
     * @return bool
     */
    protected function matchAction($rule)
    {
        if (empty($rule['actions'])) {
            return true;
        }

        if (is_array($rule['actions'])) {
            return in_array($this->action, $rule['actions'], true);
        }

        return $this->action === $rule['actions'];
    }

    /**
     * Match user
     *
     * @access protected
     *
     * @param array $rule rule definition
     *
     * @return bool
     * @throws Exception
     */
    protected function matchUser($rule)
    {
        if (empty($rule['users'])) {
            return true;
        }

        if (!is_array($rule['users'])) {
            $rule['users'][] = $rule['users'];
        }

        /** @noinspection ForeachSourceInspection */
        foreach ($rule['users'] AS $u) {
            switch ($u) {
                case '*':
                    return true;

                case '?':
                    if ($this->user->isGuest()) {
                        return true;
                    }
                    break;

                case '@':
                    if (!$this->user->isGuest()) {
                        return true;
                    }
                    break;

                default:
                    if ($this->user->getID() === $u) {
                        return true;
                    }
            }
        }

        return false;
    }

    /**
     * Match role
     *
     * @access protected
     *
     * @param array $rule rule definition
     *
     * @return bool
     * @throws Exception
     */
    protected function matchRole($rule)
    {
        if (empty($rule['roles'])) {
            return true;
        }

        if (!is_array($rule['roles'])) {
            $rule['roles'][] = $rule['roles'];
        }

        /** @noinspection ForeachSourceInspection */
        foreach ($rule['roles'] AS $role) {
            if ($this->user->check($role)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Match IP
     *
     * @access protected
     *
     * @param array $rule rule definition
     *
     * @return bool
     * @throws Exception
     */
    protected function matchIP($rule)
    {
        if (empty($rule['ips'])) {
            return true;
        }

        if (!is_array($rule['ips'])) {
            $rule['ips'][] = $rule['ips'];
        }

        $userIp = $this->request->getUserIP();

        /** @noinspection ForeachSourceInspection */
        foreach ($rule['ips'] AS $r) {
            if ($r === '*' || $r === $userIp || (($pos = strpos($r, '*')) !== false && 0 === strpos($userIp, $r))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Match verbose
     *
     * @access protected
     *
     * @param array $rule rule definition
     *
     * @return bool
     * @throws Exception
     */
    protected function matchVerb($rule)
    {
        if (empty($rule['verb'])) {
            return true;
        }

        if (!is_array($rule['verb'])) {
            $rule['verb'][] = $rule['verb'];
        }

        $verb = $this->request->getMethod();

        /** @noinspection ForeachSourceInspection */
        foreach ($rule['verb'] AS $v) {
            if ($v === $verb) {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function post(array $params)
    {
        return $params['data'];
    }
}
