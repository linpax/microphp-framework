<?php /** CsrfFilterMicro */

namespace Micro\Filter;

use Micro\Base\Exception;
use Micro\Web\ISession;
use Micro\Web\RequestInjector;
use Micro\Web\SessionInjector;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class CsrfFilter
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
class CsrfFilter extends Filter
{
    /**
     * @param array $params
     * @return bool
     * @throws Exception
     */
    public function pre(array $params)
    {
        /** @var ServerRequestInterface $session */
        $server = (new RequestInjector)->build();
        /** @var ISession $session */
        $session = (new SessionInjector)->build();

        /** @var array $params */
        $params = $server->getServerParams();
        /** @var array $post */
        $post = $server->getParsedBody();

        if ($params['REQUEST_METHOD'] !== 'POST') {
            return true;
        }

        $postCSRF = $post['csrf'];

        if (!$postCSRF) {
            $this->result = [
                'redirect' => !empty($rule['redirect']) ? $rule['redirect'] : null,
                'message' => !empty($rule['message']) ? $rule['message'] : 'Not allowed!'
            ];

            return false;
        }

        /** @var array $csrf */
        $csrf = $session->csrf;

        if (($key = in_array(md5($postCSRF), $session->csrf, true)) !== null) {
            unset($session->csrf[md5($postCSRF)]);

            $session->csrf = $csrf;

            return true;
        }

        $this->result = [
            'redirect' => !empty($rule['redirect']) ? $rule['redirect'] : null,
            'message' => !empty($rule['message']) ? $rule['message'] : 'Bad request!'
        ];

        return false;
    }

    /**
     * @inheritdoc
     */
    public function post(array $params)
    {
        return preg_replace_callback('/(<form[^>]*>)(.*?)(<\/form>)/m', [$this, 'insertProtect'], $params['data']);
    }

    /**
     * Insert CSRF protect into forms
     *
     * @access public
     *
     * @param array $matches Form
     *
     * @return string
     * @throws Exception
     */
    public function insertProtect(array $matches = [])
    {
        $gen = md5(mt_rand());

        /** @var ISession $s */
        $s = (new SessionInjector)->build();
        $s->csrf = array_merge(is_array($s->csrf) ? $s->csrf : [], [md5($gen)]);

        return $matches[1].'<input type="hidden" name="csrf" value="'.$gen.'" />'.$matches[2].$matches[3];
    }
}
