<?php /** FilterMicro */

namespace Micro\Filter;

/**
 * Filter class file.
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
abstract class Filter implements IFilter
{
    /** @var array|string|bool $result Result array */
    public $result;
    /** @var string $action Current action */
    protected $action;

    /**
     * @param string $action current action
     */
    public function __construct($action)
    {
        $this->action = $action;
    }
}
