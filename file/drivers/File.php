<?php /** MicroFile */

namespace Micro\File\Drivers;

/**
 * Class File is interface for filesystem drivers
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright (c) 2013 Oleg Lunegov
 * @license https://github.com/linpax/microphp-framework/blob/master/LICENSE
 * @package Micro
 * @subpackage File\Drivers
 * @version 1.0
 * @since 1.0
 */
abstract class File implements IFile
{
    /** @var mixed $stream File stream */
    protected $stream;


    /**
     * @inheritdoc
     */
    public function exists($filePath)
    {
        return $this->file_exists($filePath);
    }

    /**
     * @inheritdoc
     */
    public function has($filePath)
    {
        return $this->file_exists($filePath);
    }

    /**
     * @inheritdoc
     */
    public function read($filePath)
    {
        return $this->file_get_contents($filePath);
    }

    /**
     * @inheritdoc
     */
    public function getSize($filePath)
    {
        return $this->size($filePath);
    }

    /**
     * @inheritdoc
     */
    public function delete($filePath)
    {
        return $this->unlink($filePath);
    }
}
