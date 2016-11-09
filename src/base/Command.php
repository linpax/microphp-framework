<?php /** MicroCommand */

namespace Micro\Base;

/**
 * Command class file.
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright (c) 2013 Oleg Lunegov
 * @license https://github.com/linpax/microphp-framework/blob/master/LICENSE
 * @package Micro
 * @subpackage Base
 * @version 1.0
 * @since 1.0
 * @abstract
 */
abstract class Command implements ICommand
{
    /** @var array $args arguments for command */
    public $args = [];
    /** @var bool $result status of execute command */
    public $result = false;
    /** @var string $message status message of execute command */
    public $message = '';


    /**
     * Set arguments class
     *
     * @access public
     *
     * @param array $params configuration array
     *
     * @result void
     */
    public function __construct(array $params = [])
    {
        $this->args = $params;
    }
}
