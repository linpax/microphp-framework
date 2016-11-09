<?php /** MicroException */

namespace Micro\Base;

/**
 * Exception specific exception
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright (c) 2013 Oleg Lunegov
 * @license https://github.com/linpax/microphp-framework/blob/master/LICENSE
 * @package Micro
 * @subpackage Base
 * @version 1.0
 * @since 1.0
 */
class Exception extends \Exception
{
    /**
     * @return string
     */
    public function __toString()
    {
        header('HTTP/1.1 500 Internal Server Error');

        printf('<h1>%s</h1><p>In %s: %s</p><pre>%s</pre>',
            $this->message,
            $this->file,
            $this->line,
            $this->getTraceAsString()
        );

        error_reporting(0);
        exit;
    }
}
