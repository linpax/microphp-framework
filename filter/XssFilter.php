<?php /** XssFilterMicro */

namespace Micro\Filter;

/**
 * Class XssFilter
 *
 * @author Opeykin A. <andrey.opeykin.ru> &; <aopeykin@gmail.com>
 * @package Micro
 * @subpackage Filter
 * @version 1.0
 * @since 1.0
 */
class XssFilter extends Filter
{
    /**
     * @inheritdoc
     */
    public function pre(array $params)
    {
        $clean = trim(strtoupper(!empty($params['clean']) ? $params['clean'] : '*'));

        $data = ['GET' => &$_GET, 'POST' => &$_POST, 'COOKIE' => &$_COOKIE, 'FILES' => &$_FILES];
        if ($clean === '*') {
            $clean = 'GET,POST,COOKIE,FILES';
        }
        $dataForClean = explode(',', $clean);

        foreach ($dataForClean as $key => &$value) {
            if (!empty($data[$key]) && count($data[$key])) {
                $value = $this->doXssClean($data[$key]);
            }
        }

        return true;
    }

    /**
     * Do XSS Clean
     *
     * @access private
     *
     * @param array $data data for check
     *
     * @return mixed
     */
    private function doXssClean($data)
    {
        if (is_array($data) && count($data)) {
            foreach ($data as $k => &$v) {
                $v = $this->doXssClean($data[$k]);
            }

            return $data;
        }

        if (trim($data) === '') {
            return $data;
        }

        // xss_clean function from Kohana framework 2.3.1
        $data = str_replace(['&amp;', '&lt;', '&gt;'], ['&amp;amp;', '&amp;lt;', '&amp;gt;'], $data);
        $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
        $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

        // Remove any attribute starting with "on" or xmlns
        $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

        // Remove javascript: and vbscript: protocols
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu',
            '$1=$2nojavascript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu',
            '$1=$2novbscript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u',
            '$1=$2nomozbinding...', $data);

        // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i',
            '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i',
            '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu',
            '$1>', $data);

        // Remove namespaced elements (we do not need them)
        $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

        do {
            // Remove really unwanted tags
            $old_data = $data;
            $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i',
                '', $data);
        } while ($old_data !== $data);

        return $data;
    }

    /**
     * @inheritdoc
     */
    public function post(array $params)
    {
        return $params['data'];
    }
}
