<?php /** MicroFile */

namespace Micro\Mail\Transport;

use Micro\Mail\IMessage;

/**
 * File transport
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright (c) 2013 Oleg Lunegov
 * @license https://github.com/linpax/microphp-framework/blob/master/LICENSE
 * @package Micro
 * @subpackage Mail\Transport
 * @version 1.0
 * @since 1.0
 */
class File extends Transport
{
    /** @var string $mailDir */
    private $mailDir;


    /**
     * @access public
     *
     * @param array $params
     *
     * @result void
     */
    public function __construct(array $params = [])
    {
        $this->mailDir = $params[''] ?: '';
    }

    /**
     * @inheritdoc
     */
    public function send(IMessage $message)
    {
        return false;
    }
}
