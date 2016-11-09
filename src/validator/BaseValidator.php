<?php /** MicroBaseValidator */

namespace Micro\Validator;

/**
 * Class BaseValidator
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright (c) 2013 Oleg Lunegov
 * @license https://github.com/linpax/microphp-framework/blob/master/LICENSE
 * @package Micro
 * @subpackage Validator
 * @version 1.0
 * @since 1.0
 * @abstract
 */
abstract class BaseValidator extends \stdClass implements IValidator
{
    /** @var array $params */
    public $params = [];
    /** @var string[] $elements */
    public $elements = [];
    /** @var array $errors */
    public $errors = [];

    public function __construct(array $params = [])
    {
        foreach ($params AS $key => $val) {
            $this->$key = $val;
        }
    }
}
