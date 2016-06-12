<?php /** MicroRawQueue */

namespace Micro\Queue;

/**
 * RawQueue class file.
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright (c) 2013 Oleg Lunegov
 * @license https://github.com/linpax/microphp-framework/blob/master/LICENSE
 * @package Micro
 * @subpackage Queue
 * @version 1.0
 * @since 1.0
 */
class RawQueue implements IQueue
{
    /**
     * Constructor Queues
     *
     * @access public
     *
     * @param array $params Configuration array
     *
     * @result void
     */
    public function __construct(array $params = [])
    {
        // TODO: Implement __construct() method.
    }

    /**
     * @inheritdoc
     */
    public function test()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function sync($name, array $params = [])
    {
        // TODO: Implement sync() method.
    }

    /**
     * @inheritdoc
     */
    public function async($name, array $params = [])
    {
        // TODO: Implement async() method.
    }

    /**
     * @inheritdoc
     */
    public function stream($name, array $params = [])
    {
        // TODO: Implement stream() method.
    }
}
