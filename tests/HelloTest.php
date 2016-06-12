<?php /** HelloTestMicro */

namespace Micro\Tests;

/**
 * Class Hello test
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright (c) 2013 Oleg Lunegov
 * @license https://github.com/linpax/microphp-framework/blob/master/LICENSE
 * @package Micro
 * @subpackage Tests
 * @version 1.0
 * @since 1.0
 */
class HelloTest extends TestCase
{
    public function up()
    {
        //
    }

    public function down()
    {
        //
    }

    public function testHello()
    {
        static::assertEquals('200 Ok', '200 Ok');
    }
}
