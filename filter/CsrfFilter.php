<?php /** CsrfFilterMicro */

namespace Micro\Filter;

use Micro\Base\Injector;
use Micro\Web\IRequest;
use Micro\Web\ISession;

/**
 * Class CsrfFilter
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright &copy; 2013 Oleg Lunegov
 * @license /LICENSE
 * @package Micro
 * @subpackage Filter
 * @version 1.0
 * @since 1.0
 */
class CsrfFilter extends Filter
{
    /**
     * @inheritdoc
     */
    public function pre(array $params)
    {
        /** @var IRequest $request */
        $request = (new Injector)->get('request');
        /** @var ISession $session */
        $session = (new Injector)->get('session');

        if ($request->server('REQUEST_METHOD') !== 'POST') {
            return true;
        }

        $postCSRF = $request->post('csrf');

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
        return preg_replace_callback(
            '/(<form[^>]*>)(.*?)(<\/form>)/m',
            array($this, 'insertProtect'),
            $params['data']
        );
    }

    /**
     * Insert CSRF protect into forms
     *
     * @access public
     *
     * @param array $matches Form
     *
     * @return string
     */
    public function insertProtect(array $matches = [])
    {
        $gen = md5(mt_rand());
        /** @var ISession $s */
        $s = (new Injector)->get('session');

        $s->csrf = array_merge(is_array($s->csrf) ? $s->csrf : [], [md5($gen)]);

        return $matches[1].'<input type="hidden" name="csrf" value="'.$gen.'" />'.$matches[2].$matches[3];
    }
}
