<?php

namespace Micro\Tests\Base;

use Micro\Base\Autoload;
use Micro\Tests\TestCase;

class AutoloadTest extends TestCase
{
    /** @var  string $path */
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
}
