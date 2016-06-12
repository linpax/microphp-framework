<?php

/**
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright (c) 2013 Oleg Lunegov
 * @license https://github.com/linpax/microphp-framework/blob/master/LICENSE
 */

namespace Micro\Tests\Base;

use Micro\Base\Autoload;
use Micro\Tests\TestCase;

class AutoloadTest extends TestCase
{
    protected $path;

    public function setUp()
    {
        $this->path = __DIR__ . '/../..';
    }

    public function testGetAlias()
    {
        static::assertEquals(realpath(Autoload::getAlias('Micro')), realpath($this->path));
        static::assertFalse(Autoload::getAlias('Vendor'));
    }

    public function testSetAlias()
    {
        $path = '/fake/path';

        Autoload::setAlias('FakePath1', $path);
        Autoload::setAlias('FakePath2', []);

        static::assertEquals(Autoload::getAlias('FakePath1'), $path);
        static::assertEquals(Autoload::getAlias('FakePath2'), false);
    }
}
