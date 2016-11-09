<?php /** MicroConsoleResolver */

namespace Micro\Cli;

use Micro\Base\Exception;
use Micro\base\ResolverInterface;

/**
 * CLI Resolver class file.
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright (c) 2013 Oleg Lunegov
 * @license https://github.com/linpax/microphp-framework/blob/master/LICENSE
 * @package Micro
 * @subpackage Resolver
 * @version 1.0
 * @since 1.0
 */
class CliResolver implements ResolverInterface
{
    /**
     * Get instance application
     *
     * @access public
     *
     * @return Console
     */
    public function getApp()
    {
        return new Console();
    }

    /**
     * Get action from request
     *
     * @access public
     *
     * @return string
     * @throws Exception
     */
    public function getAction()
    {
        return $this->getOption('a', 'action') ?: 'default';
    }

    /**
     * Get arguments from command line
     *
     * @access public
     *
     * @param string $char -a .. -z option char
     * @param string $name --optionName_string
     * @param bool|null $required Required value?
     *
     * @return mixed
     */
    public function getOption($char = '', $name = '', $required = null)
    {
        if (!$char && !$name) {
            return false;
        }
        if ($char && (1 < strlen($char) || 1 !== preg_match('/^\w$/', $char))) {
            return false;
        }
        if ($name && (1 !== preg_match('/^\w+$/', $name))) {
            return false;
        }
        switch ($required) {
            case true:
                $char = $char ? $char.':' : $char;
                $name = $name ? $name.':' : $name;
                break;
            case false:
                $char = $char ? $char.'::' : $char;
                $name = $name ? $name.'::' : $name;
                break;
        }
        $argv = ($opts = getopt($char, [$name])) ? array_shift($opts) : [];
        return is_array($argv) ? array_shift($argv) : $argv;
    }
}
